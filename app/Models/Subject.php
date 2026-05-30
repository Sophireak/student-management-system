<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grade_id',
        'name',
        'code',
        'score_type',
        'max_score',
    ];

    // Subject belongs to a grade
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    // Helper methods
    public function isNumeric(): bool
    {
        return $this->score_type === 'numeric';
    }

    public function isGrade(): bool
    {
        return $this->score_type === 'grade';
    }

    public function isPassFail(): bool
    {
        return $this->score_type === 'pass_fail';
    }
}