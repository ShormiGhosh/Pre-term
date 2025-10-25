<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CTMark extends Model
{
    // Explicitly set table name to prevent Laravel from looking for 'c_t_marks'
    protected $table = 'ct_marks';
    
    protected $fillable = [
        'ct_schedule_id',
        'student_id',
        'course_id',
        'marks_obtained'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2'
    ];

    /**
     * A CT mark belongs to a CT schedule
     */
    public function ctSchedule()
    {
        return $this->belongsTo(CTSchedule::class);
    }

    /**
     * A CT mark belongs to a student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * A CT mark belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
