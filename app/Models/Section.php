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

    protected array $legacyAttributeAliases = [
        'sec_prog_id' => 'sec_dept_id',
    ];

    protected $fillable = [
        'sec_dept_id',
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
        return $this->belongsTo(Departments::class, 'sec_dept_id', 'dept_id');
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

    public function getAttribute($key)
    {
        return parent::getAttribute($this->legacyAttributeAliases[$key] ?? $key);
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->legacyAttributeAliases[$key] ?? $key, $value);
    }
}