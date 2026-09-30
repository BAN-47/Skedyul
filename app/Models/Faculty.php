<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Faculty extends Model
{
    use HasUuids;

    protected $table = 'faculty';
    protected $primaryKey = 'fac_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'fac_created_at';
    const UPDATED_AT = 'fac_updated_at';

       protected $fillable = [
        'fac_usr_id',

        // Department / Program
        'fac_dept_id',
        'fac_prog_id',

        // Personal Information
        'fac_first_name',
        'fac_middle_name',
        'fac_last_name',
        'fac_suffix',
        'fac_employee_id',
        'fac_gender',
        'fac_civil_status',
        'fac_dob',
        'fac_nationality',

        // Contact Information
        'fac_phone_number',
        'fac_address',

        // Faculty-only information
        'fac_employment_type',
        'fac_rank',

        // Profile
        'fac_profile_image',
        'fac_bio',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'fac_usr_id', 'usr_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'fac_dept_id', 'dept_id');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'fac_prog_id', 'prog_id');
    }
    public function workloads()
    {
        return $this->hasMany(Workload::class, 'wl_fac_id', 'fac_id');
    }

    public function studyLoads()
    {
        return $this->hasMany(Study_Load::class, 'sl_fac_id', 'fac_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'sch_fac_id', 'fac_id');
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->fac_first_name,
            $this->fac_middle_name,
            $this->fac_last_name,
            $this->fac_suffix,
        ]);
        return implode(' ', $parts);
    }
}