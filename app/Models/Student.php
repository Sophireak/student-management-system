<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'photo', 
        'phone',
        'address',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    // Full name accessor — clean, used across views
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Photo URL accessor — returns full URL or empty string
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && \Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        return '';
    }

    // Check if student has a photo
    public function hasPhoto(): bool
    {
        return $this->photo && \Storage::disk('public')->exists($this->photo);
    }

    // Student has many enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Active enrollment only — useful shortcut
    public function activeEnrollment(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    // Auto-generate student code
    public static function generateStudentId(): string
    {
        $year     = now()->year;
        $count    = static::whereYear('created_at', $year)->withTrashed()->count();
        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "STU-{$year}-{$sequence}";
    }
}