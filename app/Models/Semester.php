<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Semester extends Model
{
    use HasUuids;

    protected $table = 'semester';
    protected $primaryKey = 'sem_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only sem_created_at exists

    protected $fillable = [
        'sem_ay_id',
        'sem_name',
        'sem_start_date',
        'sem_end_date',
        'sem_is_active',
    ];

    protected $casts = [
        'sem_is_active' => 'boolean',
        'sem_start_date' => 'date',
        'sem_end_date' => 'date',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'sem_ay_id', 'ay_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'sec_sem_id', 'sem_id');
    }

    public function workloads()
    {
        return $this->hasMany(Workload::class, 'wl_sem_id', 'sem_id');
    }

    public function studyLoads()
    {
        return $this->hasMany(Study_Load::class, 'sl_sem_id', 'sem_id');
    }
}