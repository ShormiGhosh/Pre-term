<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Student Model
 * Represents a student in the system
 * Uses Laravel's Authenticatable trait for authentication features
 */
class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     * These fields can be filled using create() or fill() methods
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roll_number',
        'department',
        'year',
        'semester',
        'email_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Password should never be exposed in JSON responses
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     * Ensures password is always hashed
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
