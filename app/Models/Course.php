<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_code',
        'course_title',
        'course_credit',
        'teacher_id',
        'department',
    ];

    /**
     * Get the teacher who owns the course
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get all students enrolled in this course
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_enrollments');
    }

    /**
     * Get all enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get all CT schedules for this course
     */
    public function ctSchedules()
    {
        return $this->hasMany(CTSchedule::class);
    }

    /**
     * Get all attendance sessions for this course
     */
    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * Get all attendances for this course
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
