<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Mail\LowAttendanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Generate QR code for attendance
     * Teacher creates a new attendance session
     */
    public function generateQR(Request $request, $courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = Course::findOrFail($courseId);

        // Verify teacher owns the course
        if ($course->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Deactivate any existing active sessions for this course
        AttendanceSession::where('course_id', $courseId)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Generate unique token for QR code
        $qrToken = Str::random(32);
        
        // Create new attendance session (10 minutes duration)
        $session = AttendanceSession::create([
            'course_id' => $courseId,
            'teacher_id' => $teacher->id,
            'qr_code' => $qrToken,
            'started_at' => now(),
            'expires_at' => now()->addMinutes(10),
            'is_active' => true,
        ]);

        // Create attendance records for all enrolled students (default: absent)
        $students = $course->students;
        foreach ($students as $student) {
            Attendance::create([
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'course_id' => $courseId,
                'status' => 'absent',
                'marked_at' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'qr_token' => $qrToken,
            'expires_at' => $session->expires_at->toISOString(),
        ]);
    }

    /**
     * Get QR code session status (for live updates)
     */
    public function getSessionStatus($sessionId)
    {
        $session = AttendanceSession::with(['attendances' => function($query) {
            $query->where('status', 'present');
        }])->findOrFail($sessionId);

        $teacher = Auth::guard('teacher')->user();
        
        // Verify teacher owns the session
        if ($session->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'present_count' => $session->present_count,
            'total_students' => $session->total_students,
            'is_valid' => $session->isValid(),
            'time_remaining' => $session->isValid() ? $session->expires_at->diffInSeconds(now()) : 0,
        ]);
    }

    /**
     * Student scans QR code to mark attendance
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $student = Auth::guard('student')->user();
        
        // Find the active session with this QR token
        $session = AttendanceSession::where('qr_code', $request->qr_token)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ], 404);
        }

        // Check if session is still valid (within 10 minutes)
        if (!$session->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This QR code has expired.'
            ], 400);
        }

        // Check if student is enrolled in this course
        if (!$session->course->students()->where('student_id', $student->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ], 403);
        }

        // Mark student as present
        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($attendance) {
            $attendance->update([
                'status' => 'present',
                'marked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'marked_at' => now()->format('h:i A'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Attendance record not found.'
        ], 404);
    }

    /**
     * Get student's attendance calendar for a course
     */
    public function getStudentAttendance($courseId)
    {
        $student = Auth::guard('student')->user();
        $course = Course::findOrFail($courseId);

        // Verify student is enrolled
        if (!$course->students()->where('student_id', $student->id)->exists()) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Get all attendance sessions for this course
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->with(['attendances' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('started_at', 'desc')
            ->get();

        // Format for calendar
        $attendanceData = $sessions->map(function($session) {
            $attendance = $session->attendances->first();
            return [
                'date' => $session->started_at->format('Y-m-d'),
                'time' => $session->started_at->format('h:i A'),
                'status' => $attendance ? $attendance->status : 'absent',
                'marked_at' => $attendance && $attendance->marked_at ? 
                    $attendance->marked_at->format('h:i A') : null,
            ];
        });

        // Calculate attendance based on unique days (not individual sessions)
        $sessionsByDate = $sessions->groupBy(function($session) {
            return $session->started_at->format('Y-m-d');
        });

        $totalDays = $sessionsByDate->count();
        $presentDays = 0;

        foreach ($sessionsByDate as $date => $dateSessions) {
            // Check if student was present in ANY session on this date
            $wasPresentOnDate = $dateSessions->filter(function($session) use ($student) {
                return $session->attendances->where('student_id', $student->id)
                    ->where('status', 'present')
                    ->count() > 0;
            })->count() > 0;

            if ($wasPresentOnDate) {
                $presentDays++;
            }
        }

        // Get calculated marks if exists
        $latestAttendance = Attendance::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereNotNull('marks')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $marks = $latestAttendance ? $latestAttendance->marks : 0;

        return response()->json([
            'success' => true,
            'attendances' => $attendanceData,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            'marks' => $marks,
        ]);
    }

    /**
     * Get active session for a course (for both student and teacher)
     */
    public function getActiveSession($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Find active session
        $session = AttendanceSession::where('course_id', $courseId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No active attendance session.',
                'has_session' => false,
            ]);
        }

        // If teacher is checking, return full session details
        if (Auth::guard('teacher')->check()) {
            return response()->json([
                'success' => true,
                'has_session' => true,
                'session' => [
                    'id' => $session->id,
                    'qr_code' => $session->qr_code,
                    'started_at' => $session->started_at,
                    'expires_at' => $session->expires_at,
                ],
            ]);
        }

        // For students, verify enrollment
        $student = Auth::guard('student')->user();
        if (!$course->students()->where('student_id', $student->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ], 403);
        }

        // Check if student already marked attendance
        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        return response()->json([
            'success' => true,
            'has_session' => true,
            'already_marked' => $attendance && $attendance->status === 'present',
            'time_remaining' => $session->expires_at->diffInSeconds(now()),
        ]);
    }

    /**
     * Close attendance session manually
     */
    public function closeSession($sessionId)
    {
        $teacher = Auth::guard('teacher')->user();
        $session = AttendanceSession::findOrFail($sessionId);

        // Verify teacher owns the session
        if ($session->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $session->update(['is_active' => false]);

        // Check for low attendance and send notifications
        $this->checkAndNotifyLowAttendance($session->course_id);

        return response()->json([
            'success' => true,
            'message' => 'Attendance session closed.'
        ]);
    }

    /**
     * Check students with low attendance and send email notifications
     */
    private function checkAndNotifyLowAttendance($courseId)
    {
        $threshold = 60; // 60% attendance threshold
        $course = Course::findOrFail($courseId);
        
        // Get all students enrolled in this course
        $students = $course->students;
        
        // Get total closed attendance sessions for this course
        $totalSessions = AttendanceSession::where('course_id', $courseId)
            ->where('is_active', false)
            ->count();
        
        if ($totalSessions == 0) {
            return;
        }

        foreach ($students as $student) {
            // Count present attendances for this student
            $presentCount = Attendance::whereHas('attendanceSession', function($query) use ($courseId) {
                    $query->where('course_id', $courseId)
                          ->where('is_active', false);
                })
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();
            
            // Calculate attendance rate
            $attendanceRate = $totalSessions > 0 
                ? ($presentCount / $totalSessions) * 100 
                : 0;
            
            // Send notification if below threshold
            if ($attendanceRate < $threshold) {
                try {
                    Mail::to($student->email)->send(
                        new LowAttendanceNotification(
                            $student->name,
                            $course,
                            $attendanceRate,
                            $presentCount,
                            $totalSessions
                        )
                    );
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    \Log::error("Failed to send low attendance email to {$student->email}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get attendance sheet data for manual attendance view (teacher)
     */
    public function getAttendanceSheet($courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = Course::with('teacher')->findOrFail($courseId);

        // Verify teacher owns this course
        if ($course->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        // Get all enrolled students
        $students = $course->students()->orderBy('roll_number')->get();

        // Get all closed attendance sessions with dates (group by date)
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('is_active', false)
            ->orderBy('started_at')
            ->get()
            ->groupBy(function($session) {
                return $session->started_at->format('Y-m-d');
            });

        $attendanceDates = $sessions->keys()->toArray();

        // Build attendance data for each student
        $attendanceData = [];
        foreach ($students as $student) {
            $studentData = [
                'student_id' => $student->id,
                'roll' => $student->roll_number,
                'name' => $student->name,
                'attendance' => [],
                'total_sessions' => count($attendanceDates),
                'present_count' => 0,
                'percentage' => 0,
                'marks' => 0,
            ];

            // Get attendance for each date
            foreach ($attendanceDates as $date) {
                $sessionIds = $sessions[$date]->pluck('id')->toArray();
                
                // Check if student was present on this date (any session on that date)
                $attendance = Attendance::whereIn('attendance_session_id', $sessionIds)
                    ->where('student_id', $student->id)
                    ->where('status', 'present')
                    ->first();

                $studentData['attendance'][$date] = $attendance ? 'P' : 'A';
                
                if ($attendance) {
                    $studentData['present_count']++;
                }
            }

            // Calculate percentage
            if ($studentData['total_sessions'] > 0) {
                $studentData['percentage'] = round(($studentData['present_count'] / $studentData['total_sessions']) * 100, 2);
            }

            // Get calculated marks if exists
            $latestAttendance = Attendance::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->whereNotNull('marks')
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if ($latestAttendance) {
                $studentData['marks'] = $latestAttendance->marks;
            }

            $attendanceData[] = $studentData;
        }

        return response()->json([
            'success' => true,
            'dates' => $attendanceDates,
            'students' => $attendanceData,
            'total_marks' => $course->attendance_total_marks ?? 10,
        ]);
    }

    /**
     * Calculate and save attendance marks based on percentage
     */
    public function calculateAttendanceMarks($courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = Course::findOrFail($courseId);

        // Verify teacher owns this course
        if ($course->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $totalMarks = $course->attendance_total_marks ?? 10;
        $students = $course->students;

        // Get total closed sessions
        $totalSessions = AttendanceSession::where('course_id', $courseId)
            ->where('is_active', false)
            ->get()
            ->groupBy(function($session) {
                return $session->started_at->format('Y-m-d');
            })
            ->count();

        if ($totalSessions == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance sessions found.'
            ], 400);
        }

        foreach ($students as $student) {
            // Count unique dates present
            $sessionIds = AttendanceSession::where('course_id', $courseId)
                ->where('is_active', false)
                ->pluck('id');

            $presentCount = Attendance::whereIn('attendance_session_id', $sessionIds)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->get()
                ->groupBy(function($attendance) {
                    return $attendance->attendanceSession->started_at->format('Y-m-d');
                })
                ->count();

            // Calculate percentage
            $percentage = $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0;

            // Calculate marks based on attendance percentage with fixed grading scheme
            $marks = 0;
            if ($percentage >= 90) {
                $marks = 5;
            } elseif ($percentage >= 80) {
                $marks = 4;
            } elseif ($percentage >= 70) {
                $marks = 3;
            } elseif ($percentage >= 65) {
                $marks = 2;
            } elseif ($percentage >= 60) {
                $marks = 1;
            } else {
                $marks = 0;
            }

            // Update marks in all attendance records for this student in this course
            Attendance::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->update(['marks' => $marks]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance marks calculated and saved successfully!'
        ]);
    }
}
