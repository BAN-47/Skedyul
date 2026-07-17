<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';
    protected $primaryKey = 'sch_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'sch_created_at';
    const UPDATED_AT = 'sch_updated_at';

    protected $fillable = [
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
        'sch_created_by'
    ];
    
  protected $casts = [
        'sch_is_active'   => 'boolean',
        'sch_created_at'  => 'datetime',
        'sch_updated_at'  => 'datetime',
    ];
 
    // ── Relationships ──────────────────────────────────────────────────────
 
    public function studyLoad()
    {
        return $this->belongsTo(Study_Load::class, 'sch_load_id', 'sl_id');
    }
 
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'sch_fac_id', 'fac_id');
    }
 
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'sch_subj_id', 'subj_id');
    }
 
    public function section()
    {
        return $this->belongsTo(Section::class, 'sch_sec_id', 'sec_id');
    }
 
    public function room()
    {
        return $this->belongsTo(Room::class, 'sch_room_id', 'room_id');
    }
 
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'sch_sem_id', 'sem_id');
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'sch_created_by', 'usr_id');
    }
}

?>
