<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowAttendanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $course;
    public $attendanceRate;
    public $presentCount;
    public $totalSessions;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, Course $course, $attendanceRate, $presentCount, $totalSessions)
    {
        $this->studentName = $studentName;
        $this->course = $course;
        $this->attendanceRate = $attendanceRate;
        $this->presentCount = $presentCount;
        $this->totalSessions = $totalSessions;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Low Attendance Alert - ' . $this->course->course_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low-attendance',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
