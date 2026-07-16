<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study_Load extends Model
{
    protected $table = 'study_load';
    protected $primaryKey = 'sl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // study_load only has sl_assigned_at (with a DB-level DEFAULT NOW()),
    // not a created_at/updated_at pair — so disable Eloquent's auto timestamps.
    public $timestamps = false;

    protected $fillable = [
        'sl_fac_id',
        'sl_subj_id',
        'sl_sec_id',
        'sl_sem_id',
        'sl_assigned_by',
    ];

    public function faculty() { return $this->belongsTo(Faculty::class, 'sl_fac_id', 'fac_id'); }
    public function subject() { return $this->belongsTo(Subjects::class, 'sl_subj_id', 'subj_id'); }
    public function section() { return $this->belongsTo(Section::class, 'sl_sec_id', 'sec_id'); }
}