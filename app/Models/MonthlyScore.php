<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyScore extends Model
{
    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'academic_year_id',
        'month',
        'score',
        'grade',
        'pass_fail',
        'entered_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'score'     => 'decimal:2',
            'locked_at' => 'datetime',
            'month'     => 'integer',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    // Human-readable month name in Cambodian school calendar
    public static function monthName(int $month): string
    {
        return match($month) {
            1 => 'September',
            2 => 'October',
            3 => 'November',
            4 => 'December',
            5 => 'January',
            6 => 'February',
            7 => 'March',
            8 => 'April',
            9 => 'May',
            default => 'Unknown',
        };
    }

    // Which months belong to which semester
    public static function semesterMonths(int $semester): array
    {
        return match($semester) {
            1 => [1, 2, 3, 4, 5],   // Sep–Jan
            2 => [6, 7, 8, 9],       // Feb–May
            default => [],
        };
    }
}