<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_code',
        'date_of_birth',
        'gender',
        'program',
        'status',
    ];

    // A student belongs to one user account
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A student can be enrolled in many courses
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}