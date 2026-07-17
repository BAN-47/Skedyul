<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Schedule_Submission extends Model
{
    protected $table      = 'schedule_submission';
    protected $primaryKey = 'schsub_id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->schsub_id = $m->schsub_id ?: (string) Str::uuid());
    }

    protected $fillable = [
        'schsub_id',
        'schsub_dept_id',
        'schsub_sem_id',
        'schsub_submitted_by',
        'schsub_submitted_at',
        'schsub_reviewed_by',
        'schsub_reviewed_at',
        'schsub_status',
        'schsub_remarks',
    ];

    protected $casts = [
        'schsub_submitted_at' => 'datetime',
        'schsub_reviewed_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function department()
    {
        return $this->belongsTo(Department::class, 'schsub_dept_id', 'dept_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'schsub_sem_id', 'sem_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'schsub_submitted_by', 'usr_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'schsub_reviewed_by', 'usr_id');
    }

    // Schedules that belong to this submission's department + semester
    public function schedules()
    {
        return Schedule::where('sch_sem_id', $this->schsub_sem_id)
            ->whereHas('faculty', fn($q) => $q->where('fac_dept_id', $this->schsub_dept_id))
            ->get();
    }
}