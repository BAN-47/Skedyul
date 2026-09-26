<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Section extends Model
{
    use HasUuids;

    protected $table = 'section';
    protected $primaryKey = 'sec_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'sec_created_at';
    const UPDATED_AT = 'sec_updated_at';

    protected $fillable = [
        'sec_prog_id',
        'sec_ay_id',
        'sec_sem_id',
        'sec_name',
        'sec_year_level',
        'sec_no_of_student',
        'sec_max_capacity',
        'sec_status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'sec_prog_id', 'prog_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'sec_ay_id', 'ay_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'sec_sem_id', 'sem_id');
    }

    public function studyLoads()
    {
        return $this->hasMany(Study_Load::class, 'sl_sec_id', 'sec_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'sch_sec_id', 'sec_id');
    }
}