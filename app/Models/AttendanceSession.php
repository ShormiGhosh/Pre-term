<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'qr_code',
        'started_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * A session belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * A session belongs to a teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * A session has many attendances
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if session is still valid
     */
    public function isValid()
    {
        return $this->is_active && now()->lessThan($this->expires_at);
    }

    /**
     * Get count of present students
     */
    public function getPresentCountAttribute()
    {
        return $this->attendances()->where('status', 'present')->count();
    }

    /**
     * Get count of total enrolled students
     */
    public function getTotalStudentsAttribute()
    {
        return $this->course->students()->count();
    }
}
