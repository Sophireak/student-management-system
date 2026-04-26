<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'status',
    ];

    // An enrollment belongs to one student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // An enrollment belongs to one course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}