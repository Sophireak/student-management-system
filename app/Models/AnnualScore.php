<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualScore extends Model
{
    protected $fillable = [
        'enrollment_id',
        'academic_year_id',
        'semester1_avg',
        'semester2_avg',
        'semester1_conduct',
        'semester2_conduct',
        'final_score',
        'is_passing',
        'rank',
        'notes',
        'is_manual_override',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'semester1_avg'      => 'decimal:2',
            'semester2_avg'      => 'decimal:2',
            'final_score'        => 'decimal:2',
            'is_passing'         => 'boolean',
            'is_manual_override' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    // Cambodian pass threshold
    public const PASS_THRESHOLD = 50.00;

    public static function isPassingScore(float $score): bool
    {
        return $score >= self::PASS_THRESHOLD;
    }
}
