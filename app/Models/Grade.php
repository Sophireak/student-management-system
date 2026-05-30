<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'level',
    ];

    // Grade has many subjects
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    // Grade has many classes
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}