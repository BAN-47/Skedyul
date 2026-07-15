<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token_Blacklist extends Model {

    protected $table = 'token_blacklist';
    protected $primaryKey = 'tbl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tbl_usr_id',
        'tbl_token',
        'tbl_revoked_at'
    ];
}

?>