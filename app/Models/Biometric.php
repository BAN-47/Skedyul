<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biometric extends Model
{
    protected $table = 'biometric_credential';
    protected $primaryKey = 'bio_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bio_usr_id',
        'bio_fingerprint_hash',
        'bio_registered_at',
        'bio_is_active'
    ];
}

?>