<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterScore extends Model
{
    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'academic_year_id',
        'semester',
        'score',
        'grade',
        'pass_fail',
        'total_score',
        'average_score',
        'rank',
        'is_manual_override',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'score'              => 'decimal:2',
            'total_score'        => 'decimal:2',
            'average_score'      => 'decimal:2',
            'is_manual_override' => 'boolean',
            'semester'           => 'integer',
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

    // Which months belong to each semester
    public static function semesterMonths(int $semester): array
    {
        return match($semester) {
            1 => [1, 2, 3, 4, 5],
            2 => [6, 7, 8, 9],
            default => [],
        };
    }

    public static function semesterLabel(int $semester): string
    {
        return match($semester) {
            1 => 'Semester 1 (September – January)',
            2 => 'Semester 2 (February – May)',
            default => 'Unknown',
        };
    }
}