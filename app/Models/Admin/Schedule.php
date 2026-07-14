<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'SCHEDULE';
    protected $primaryKey = 'sch_id';
    public $incrementing = false;
    protected $keyType = 'string';
 
    const CREATED_AT = 'sch_created_at';
    const UPDATED_AT = 'sch_updated_at';
 
    protected $fillable = [
        'sch_id',
        'sch_load_id',
        'sch_fac_id',
        'sch_subj_id',
        'sch_sec_id',
        'sch_room_id',
        'sch_sem_id',
        'sch_day',
        'sch_start_time',
        'sch_end_time',
        'sch_is_active',
        'sch_created_by',
    ];
}