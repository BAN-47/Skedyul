<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $primaryKey = 'dept_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'dept_created_at';

    protected $fillable = [
        'dept_name',
        'dept_code'
    ];

}

?>