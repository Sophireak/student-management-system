<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'subject_id',
        'name',
        'type',
        'term',
        'exam_date',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'max_score' => 'decimal:2',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    // Human-readable label used in views
    public function getFullLabelAttribute(): string
    {
        $term = $this->term
            ? ' — ' . ucfirst(str_replace('term', 'Term ', $this->term))
            : '';

        return "{$this->name}{$term}";
    }
}
