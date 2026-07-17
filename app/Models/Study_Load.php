<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Study_Load extends Model
{
    protected $table      = 'study_load';
    protected $primaryKey = 'sl_id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->sl_id = $m->sl_id ?: (string) Str::uuid());
    }

    protected $fillable = [
        'sl_id',
        'sl_fac_id',
        'sl_subj_id',
        'sl_sec_id',
        'sl_sem_id',
        'sl_assigned_by',
        'sl_assigned_at',
    ];

    protected $casts = [
        'sl_assigned_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'sl_fac_id', 'fac_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'sl_subj_id', 'subj_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'sl_sec_id', 'sec_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'sl_sem_id', 'sem_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'sl_assigned_by', 'usr_id');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'sch_load_id', 'sl_id');
    }
}
