<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'subject_id',
        'session_date',
        'period',
        'topic',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    // Session belongs to a class
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Session belongs to a subject
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Session has many attendance records
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}