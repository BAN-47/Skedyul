<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    protected $fillable = [
        'name',
        'day_cap',
        'evening_cap',
        'max_faculty_count',
    ];

    protected $casts = [
        'day_cap' => 'integer',
        'evening_cap' => 'integer',
        'max_faculty_count' => 'integer',
    ];

    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class, 'fac_special_position', 'name');
    }
}
