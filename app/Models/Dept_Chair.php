<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department_Chair extends Model {

    protected $table = 'department_chair';
    protected $primaryKey = 'dc_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'dc_usr_id',
        'dc_dept_id',
        'dc_assigned_at'
    ];
}

?>