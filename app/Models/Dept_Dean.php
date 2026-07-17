<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dept_Dean extends Model
{
    protected $table = 'department_dean';
    protected $primaryKey = 'dd_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($m) => $m->dd_id = $m->dd_id ?: (string) Str::uuid());
    }

    protected $fillable = [
        'dd_usr_id',
        'dd_dept_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dd_usr_id', 'usr_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dd_dept_id', 'dept_id');
    }
}