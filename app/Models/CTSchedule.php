<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CTSchedule extends Model
{
    protected $table = 'ct_schedules';
    
    protected $fillable = [
        'course_id',
        'ct_name',
        'ct_datetime',
        'total_marks',
        'description',
        'email_sent'
    ];

    protected $casts = [
        'ct_datetime' => 'datetime',
        'email_sent' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function isUpcoming()
    {
        return $this->ct_datetime > now();
    }

    public function isPast()
    {
        return $this->ct_datetime <= now();
    }
}
