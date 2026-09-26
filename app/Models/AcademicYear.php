<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AcademicYear extends Model
{
    use HasUuids;

    protected $table = 'academic_year';
    protected $primaryKey = 'ay_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only ay_created_at exists

    protected $fillable = [
        'ay_academic_year',
        'ay_year_label',
        'ay_is_active',
    ];

    protected $casts = [
        'ay_is_active' => 'boolean',
    ];

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'sem_ay_id', 'ay_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'sec_ay_id', 'ay_id');
    }

    public function workloads()
    {
        return $this->hasMany(Workload::class, 'wl_ay_id', 'ay_id');
    }
}