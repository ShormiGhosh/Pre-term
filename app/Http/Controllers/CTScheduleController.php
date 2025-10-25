<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CTSchedule;
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
}
