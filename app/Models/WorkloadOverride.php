<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkloadOverride extends Model
{
    protected $table = 'workload_overrides';
    protected $primaryKey = 'wlo_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'wlo_id',
        'wlo_load_id',
        'wlo_fac_id',
        'wlo_course_id',
        'wlo_section_id',
        'wlo_semester_id',
        'wlo_approved_by',
        'wlo_shift',
        'wlo_designation',
        'wlo_hours_before',
        'wlo_hours_added',
        'wlo_hours_after',
        'wlo_cap_hours',
        'wlo_reason',
    ];

    protected $casts = [
        'wlo_hours_before' => 'float',
        'wlo_hours_added' => 'float',
        'wlo_hours_after' => 'float',
        'wlo_cap_hours' => 'float',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'wlo_fac_id', 'fac_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'wlo_course_id', 'course_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'wlo_section_id', 'sec_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'wlo_approved_by', 'usr_id');
    }
}
