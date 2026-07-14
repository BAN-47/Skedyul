<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';
    protected $primaryKey = 'prog_id';

    const CREATED_AT = 'prog_created_at';

    protected $fillable = [
        'prog_dept_id',
        'prog_name',
        'prog_code'
    ];
}