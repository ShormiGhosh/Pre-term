<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Mail\LowAttendanceNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckLowAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-low {--course-id= : Check specific course only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check students with attendance below 60% and send email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for students with low attendance...');
        
        $threshold = 60; // 60% attendance threshold
        $notificationsSent = 0;

        // Get courses to check
        $courses = $this->option('course-id') 
            ? Course::where('id', $this->option('course-id'))->get()
            : Course::all();

        foreach ($courses as $course) {
            $this->info("Checking course: {$course->course_code} - {$course->course_title}");
            
            // Get all students enrolled in this course
            $students = $course->students;
            
            // Get total attendance sessions for this course
            $totalSessions = AttendanceSession::where('course_id', $course->id)
                ->where('is_active', false) // Only count closed sessions
                ->count();
            
            if ($totalSessions == 0) {
                $this->warn("  No closed attendance sessions found for this course. Skipping...");
                continue;
            }

            foreach ($students as $student) {
                // Count present attendances for this student
                $presentCount = Attendance::whereHas('attendanceSession', function($query) use ($course) {
                        $query->where('course_id', $course->id)
                              ->where('is_active', false);
                    })
                    ->where('student_id', $student->id)
                    ->where('status', 'present')
                    ->count();
                
                // Calculate attendance rate
                $attendanceRate = $totalSessions > 0 
                    ? ($presentCount / $totalSessions) * 100 
                    : 0;
                
                // Check if below threshold
                if ($attendanceRate < $threshold) {
                    $this->warn("  ⚠️  {$student->name}: {$presentCount}/{$totalSessions} ({$attendanceRate}%)");
                    
                    // Send email notification
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
                        
                        $this->info("     ✅ Email sent to {$student->email}");
                        $notificationsSent++;
                    } catch (\Exception $e) {
                        $this->error("     ❌ Failed to send email: " . $e->getMessage());
                    }
                } else {
                    $this->info("  ✓ {$student->name}: {$presentCount}/{$totalSessions} ({$attendanceRate}%)");
                }
            }
        }

        $this->info("\n✅ Process completed!");
        $this->info("Total notifications sent: {$notificationsSent}");

        return Command::SUCCESS;
    }
}
