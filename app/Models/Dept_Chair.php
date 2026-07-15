<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept_Chair extends Model
{
    protected $table = 'department_chair';
    protected $primaryKey = 'dc_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'dc_usr_id',
        'dc_dept_id',
    ];
}