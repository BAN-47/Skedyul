<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'USER'; // keep only if the table itself was created with quotes preserving uppercase
    protected $primaryKey = 'usr_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'usr_id',
        'usr_name',
        'usr_email',
        'usr_password_hash',
        'usr_role',
        'usr_is_active',
    ];

    protected $hidden = [
        'usr_password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->usr_password_hash;
    }

    public function deptChairRecord()
    {
    return $this->hasOne(Dept_Chair::class, 'dc_usr_id', 'usr_id');
    }
}