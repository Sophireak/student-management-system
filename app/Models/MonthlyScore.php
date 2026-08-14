<?php

namespace App\Models;

use App\Helpers\AcademicCalendar;
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

    /**
     * Human-readable month name (Cambodia academic calendar)
     */
    public static function monthName(int $month): string
    {
        return AcademicCalendar::monthName($month);
    }

    /**
     * Get months for a semester
     */
    public static function semesterMonths(int $semester): array
    {
        return AcademicCalendar::semesterMonths($semester);
    }
}