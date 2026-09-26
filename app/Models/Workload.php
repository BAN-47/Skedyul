<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Workload extends Model
{
    use HasUuids;

    protected $table = 'workload';
    protected $primaryKey = 'wl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'wl_created_at';
    const UPDATED_AT = 'wl_updated_at';

    protected $fillable = [
        'wl_fac_id',
        'wl_sem_id',
        'wl_ay_id',
        'wl_type',
        'wl_total_hours',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'wl_fac_id', 'fac_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'wl_sem_id', 'sem_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'wl_ay_id', 'ay_id');
    }
}