<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
    ];

    /**
     * Get the student who enrolled
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that was enrolled in
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
