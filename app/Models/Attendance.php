<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'course_id',
        'status',
        'marked_at',
        'marks',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    /**
     * An attendance belongs to a session
     */
    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    /**
     * An attendance belongs to a student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * An attendance belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
