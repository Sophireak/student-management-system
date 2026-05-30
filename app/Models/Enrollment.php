<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'enrolled_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'date',
        ];
    }

    // Enrollment belongs to a student
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Enrollment belongs to a class
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Enrollment has many scores
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    // Enrollment has many attendance records
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}