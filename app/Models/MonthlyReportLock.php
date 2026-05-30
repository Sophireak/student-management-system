<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReportLock extends Model
{
    protected $fillable = [
        'class_id',
        'academic_year_id',
        'month',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function lockedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}