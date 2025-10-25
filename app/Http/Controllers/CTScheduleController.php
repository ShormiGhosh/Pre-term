<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CTSchedule;
use App\Models\CTMark;
use App\Mail\CTScheduleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CTScheduleController extends Controller
{
    public function store(Request $request, $courseId)
    {
        $course = Course::with('students')->findOrFail($courseId);
        
        // Verify teacher owns this course
        if ($course->teacher_id !== Auth::guard('teacher')->id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'ct_name' => 'required|string|max:255',
            'ct_datetime' => 'required|date|after:now',
            'total_marks' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $ctSchedule = CTSchedule::create([
            'course_id' => $courseId,
            'ct_name' => $validated['ct_name'],
            'ct_datetime' => $validated['ct_datetime'],
            'total_marks' => $validated['total_marks'],
            'description' => $validated['description'] ?? null,
        ]);

        // Send email to all enrolled students
        foreach ($course->students as $student) {
            try {
                Mail::to($student->email)->send(
                    new CTScheduleNotification($ctSchedule, $student->name)
                );
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error('Failed to send CT notification to ' . $student->email . ': ' . $e->getMessage());
            }
        }

        // Mark email as sent
        $ctSchedule->update(['email_sent' => true]);

        return redirect()->back()->with('success', 'CT scheduled successfully and notifications sent to all students!');
    }

    public function destroy($id)
    {
        $ctSchedule = CTSchedule::with('course')->findOrFail($id);
        
        // Verify teacher owns this course
        if ($ctSchedule->course->teacher_id !== Auth::guard('teacher')->id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $ctSchedule->delete();

        return redirect()->back()->with('success', 'CT schedule deleted successfully!');
    }

    public function markAsPast($id)
    {
        $ctSchedule = CTSchedule::findOrFail($id);
        
        // Return success - the CT will automatically be in past section
        // since the frontend checks ct_datetime vs current time
        return response()->json([
            'success' => true,
            'message' => 'CT marked as past'
        ]);
    }

    /**
     * Save CT marks for all students
     */
    public function saveMarks(Request $request, $courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = Course::findOrFail($courseId);
        
        // Verify teacher owns this course
        if ($course->teacher_id !== $teacher->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.ct_schedule_id' => 'required|exists:ct_schedules,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0',
        ]);

        $studentsNotified = [];

        foreach ($validated['marks'] as $markData) {
            // Get total marks for validation
            $ctSchedule = CTSchedule::find($markData['ct_schedule_id']);
            
            // Validate marks don't exceed total
            if (isset($markData['marks_obtained']) && $markData['marks_obtained'] > $ctSchedule->total_marks) {
                return redirect()->back()->with('error', 
                    "Marks cannot exceed total marks ({$ctSchedule->total_marks}) for {$ctSchedule->ct_name}");
            }

            // Update or create mark
            $mark = CTMark::updateOrCreate(
                [
                    'ct_schedule_id' => $markData['ct_schedule_id'],
                    'student_id' => $markData['student_id'],
                    'course_id' => $courseId
                ],
                [
                    'marks_obtained' => $markData['marks_obtained'] ?? null
                ]
            );

            // Create notification for student if marks were actually saved (not null)
            if (isset($markData['marks_obtained']) && !in_array($markData['student_id'], $studentsNotified)) {
                \App\Models\Notification::create([
                    'student_id' => $markData['student_id'],
                    'type' => 'ct_marks_added',
                    'title' => 'CT Marks Added',
                    'message' => "Your marks for {$ctSchedule->ct_name} in {$course->course_code} have been uploaded by your teacher.",
                    'icon' => null,
                    'link' => route('student.courses.show', $courseId)
                ]);
                
                $studentsNotified[] = $markData['student_id'];
            }
        }

        return redirect()->back()->with('success', 'CT marks saved successfully!');
    }

    /**
     * Download CT marks as PDF
     */
    public function downloadMarks($courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = Course::with(['students', 'ctSchedules'])->findOrFail($courseId);
        
        // Verify teacher owns this course
        if ($course->teacher_id !== $teacher->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Get all CT schedules for this course
        $ctSchedules = $course->ctSchedules()->orderBy('ct_datetime', 'asc')->get();
        
        // Get all enrolled students
        $students = $course->students()->orderBy('roll_number', 'asc')->get();
        
        // Prepare marks data
        $marksData = [];
        foreach ($students as $student) {
            $studentMarks = [
                'roll' => $student->roll_number,
                'name' => $student->name,
                'marks' => []
            ];
            
            foreach ($ctSchedules as $ct) {
                $mark = CTMark::where('student_id', $student->id)
                             ->where('ct_schedule_id', $ct->id)
                             ->first();
                             
                $studentMarks['marks'][$ct->id] = $mark ? $mark->marks_obtained : '-';
            }
            
            $marksData[] = $studentMarks;
        }
        
        // Calculate class averages
        $classAverages = [];
        foreach ($ctSchedules as $ct) {
            $marks = CTMark::where('ct_schedule_id', $ct->id)
                          ->whereNotNull('marks_obtained')
                          ->pluck('marks_obtained');
                          
            $classAverages[$ct->id] = $marks->count() > 0 ? round($marks->avg(), 2) : '-';
        }

        // For now, return view (we'll add PDF generation later)
        return view('courses.ct-marks-pdf', compact('course', 'ctSchedules', 'students', 'marksData', 'classAverages'));
    }
}
