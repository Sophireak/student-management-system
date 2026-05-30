<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassTeacher extends Model
{
    protected $table = 'class_teachers';

    protected $fillable = [
        'class_id',
        'teacher_id',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // Pivot belongs to a class
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Pivot belongs to a teacher
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}