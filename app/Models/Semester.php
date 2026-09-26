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
    public $timestamps = false;

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
}