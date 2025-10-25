<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return response()->json([
            'success' => true,
            'attendances' => $attendanceData,
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

        return response()->json([
            'success' => true,
            'message' => 'Attendance session closed.'
        ]);
    }
}
