<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CTSchedule;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    /**
     * Export student's CT schedules to Google Calendar (iCal format)
     */
    public function exportToGoogleCalendar()
    {
        $student = Auth::guard('student')->user();
        
        // Get all enrolled courses
        $courseIds = $student->courses->pluck('id');
        
        // Get all upcoming CT schedules for enrolled courses
        $ctSchedules = CTSchedule::whereIn('course_id', $courseIds)
            ->where('ct_datetime', '>=', now())
            ->orderBy('ct_datetime', 'asc')
            ->with('course')
            ->get();
        
        // Generate Google Calendar Add URL for each CT
        $calendarLinks = collect(); // Use collection instead of array
        foreach ($ctSchedules as $ct) {
            $calendarLinks->push([
                'ct_name' => $ct->ct_name,
                'course' => $ct->course->course_code,
                'datetime' => $ct->ct_datetime,
                'google_url' => $this->generateGoogleCalendarUrl($ct)
            ]);
        }
        
        return view('student.google-calendar', compact('calendarLinks'));
    }
    
    /**
     * Generate Google Calendar add event URL
     */
    private function generateGoogleCalendarUrl($ctSchedule)
    {
        $course = $ctSchedule->course;
        
        // Event details
        $title = "{$ctSchedule->ct_name} - {$course->course_code}";
        $description = "CT Exam for {$course->course_title}\nTotal Marks: {$ctSchedule->total_marks}";
        $location = "Exam Hall"; // You can customize this
        
        // Format datetime for Google Calendar (YYYYMMDDTHHmmss)
        $startTime = $ctSchedule->ct_datetime->format('Ymd\THis');
        $endTime = $ctSchedule->ct_datetime->addHours(2)->format('Ymd\THis'); // 2 hour exam
        
        // Build Google Calendar URL
        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'details' => $description,
            'location' => $location,
            'dates' => $startTime . '/' . $endTime
        ];
        
        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }
    
    /**
     * Quick add single CT to Google Calendar
     */
    public function addSingleCT($ctId)
    {
        $student = Auth::guard('student')->user();
        $ct = CTSchedule::with('course')->findOrFail($ctId);
        
        // Verify student is enrolled in this course
        if (!$student->courses->contains($ct->course_id)) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }
        
        $googleUrl = $this->generateGoogleCalendarUrl($ct);
        
        return redirect($googleUrl);
    }
}
