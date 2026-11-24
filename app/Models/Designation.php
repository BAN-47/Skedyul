<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Designation extends Model
{
    use HasUuids;
    protected $table = 'designations';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

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

    public static function boot()
    {
        parent::boot();

        static::saving(function ($designation) {
            // Ensure null is saved if input is empty, not 0
            if (request()->has('max_faculty_count') && request()->input('max_faculty_count') === '') {
                $designation->max_faculty_count = null;
            }
        });
    }

    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class, 'fac_special_position', 'name');
    }

    /**
     * Check if assigning another faculty member to this designation would exceed the limit.
     */
    public static function isLimitReached(string $name, ?string $excludeFacultyId = null): bool
    {
        $designation = self::where('name', $name)->first();
        if (!$designation || $designation->max_faculty_count === null) {
            return false;
        }

        $count = Faculty::where('fac_special_position', $name)
            ->when($excludeFacultyId, fn($q) => $q->where('fac_id', '!=', $excludeFacultyId))
            ->count();

        return $count >= $designation->max_faculty_count;
    }
}
