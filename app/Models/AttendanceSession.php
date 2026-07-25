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
    'locked_at',
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
    protected $casts = [
    'session_date' => 'date',
    'locked_at'    => 'datetime',
];

/**
 * Check if session is locked (cannot be edited)
 */
public function isLocked(): bool
{
    return $this->locked_at !== null;
}

/**
 * Check if a user can edit this session
 */
public function canEdit($user): bool
{
    // Admin can always edit
    if ($user->isAdmin()) {
        return true;
    }

    // Teacher: locked if session is locked
    if ($this->isLocked()) {
        return false;
    }

    // Teacher: locked if past date
    if ($this->isPast()) {
        return false;
    }

    // Teacher: locked if future date
    if ($this->session_date->isFuture()) {
        return false;
    }

    return true;
}

/**
 * Auto-lock this session
 */
public function autoLock(): void
{
    if (!$this->isLocked()) {
        $this->update(['locked_at' => now()]);
    }
}
/**
 * Check if session is in the past
 */
public function isPast(): bool
{
    return $this->session_date->isPast() 
        && !$this->session_date->isToday();
}
}