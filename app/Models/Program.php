<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Program extends Model
{
    use HasUuids;

    protected $table = 'program';
    protected $primaryKey = 'prog_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only prog_created_at exists

    protected $fillable = [
        'prog_dept_id',
        'prog_name',
        'prog_code',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'prog_dept_id', 'dept_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'sec_prog_id', 'prog_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subjects::class, 'subj_prog_id', 'prog_id');
    }
}