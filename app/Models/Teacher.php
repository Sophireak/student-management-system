<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'phone',
        'address',
        'date_of_birth',
        'gender',
    ];
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_primary'    => 'boolean',
        ];
    }

    // Teacher belongs to a user account
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Teacher is assigned to many classes
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_teachers',   // pivot table
            'teacher_id',       // FK for this model (Teacher) on pivot
            'class_id'          // FK for related model (SchoolClass) on pivot
        )
            ->withPivot('is_primary')
            ->withTimestamps()
            ->whereNull('classes.deleted_at');
    }

    // Teacher's direct class_teacher pivot records
    public function classTeachers(): HasMany
    {
        return $this->hasMany(ClassTeacher::class, 'teacher_id');
    }
}
