<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study_Load extends Model {

    protected $table = 'study_load';
    protected $primaryKey = 'sl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sl_fac_id',
        'sl_subj_id',
        'sl_sec_id',
        'sl_sem_id',
        'sl_assigned_by',
        'sl_assigned_at'
    ];
}

?>