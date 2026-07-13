<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'USER';


    protected $primaryKey = 'USR_ID';


    public $incrementing = false;


    protected $keyType = 'string';



    protected $fillable = [

        'usr_name',
        'usr_email',
        'usr_password_hash',
        'usr_role',
        'usr_is_active'

    ];


    protected $hidden = [

        'USR_PASSWORD_HASH'

    ];


    public function getAuthPassword()
    {
        return $this->USR_PASSWORD_HASH;
    }


}