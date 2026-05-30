<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'academic_year_id',
        'grade_id',
        'name',
        'capacity',
    ];

    // Class belongs to an academic year
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Class belongs to a grade
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    // Class has many enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    // Class has many teachers via pivot
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'class_teachers',   // pivot table
            'class_id',         // FK for this model (SchoolClass) on pivot
            'teacher_id'        // FK for related model (Teacher) on pivot
        )
            ->withPivot('is_primary')
            ->withTimestamps()
            ->whereNull('teachers.deleted_at');
    }

    // Class has many exam sessions
    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class, 'class_id');
    }

    // Class has many attendance sessions
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'class_id');
    }

    // Direct class_teacher pivot records
    public function classTeachers(): HasMany
    {
        return $this->hasMany(ClassTeacher::class, 'class_id');
    }
}
