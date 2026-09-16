<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Department extends Model
{
    use HasUuids;

    protected $table = 'department';
    protected $primaryKey = 'dept_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only dept_created_at exists

    protected $fillable = [
        'dept_name',
        'dept_code',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class, 'prog_dept_id', 'dept_id');
    }

    public function faculty()
    {
        return $this->hasMany(Faculty::class, 'fac_dept_id', 'dept_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subjects::class, 'subj_dept_id', 'dept_id');
    }

    public function deptChairs()
    {
        return $this->hasMany(Dept_Chair::class, 'dc_dept_id', 'dept_id');
    }
}