<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit_Log extends Model
{
    protected $table = 'audit_log';
    protected $primaryKey = 'al_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'al_created_at';

    protected $fillable = [
        'al_usr_id',
        'al_action',
        'al_target_table',
        'al_target_id',
        'al_description',
        'al_ip_address'
    ];
}

?>