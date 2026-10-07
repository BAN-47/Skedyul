<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Schedule_Submission extends Model
{
    protected $table = 'schedule_submission';
    protected $primaryKey = 'schsub_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->schsub_id)) {
                $model->schsub_id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'schsub_id',
        'schsub_dept_id',
        'schsub_fac_id',
        'schsub_sem_id',
        'schsub_submitted_by',
        'schsub_submitted_at',
        'schsub_reviewed_by',
        'schsub_reviewed_at',
        'schsub_status',
        'schsub_remarks',
        'schsub_schedule_snapshot',
    ];

    protected $casts = [
        'schsub_submitted_at' => 'datetime',
        'schsub_reviewed_at' => 'datetime',
        'schsub_schedule_snapshot' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Departments::class, 'schsub_dept_id', 'dept_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'schsub_fac_id', 'fac_id');
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

    public function schedules()
    {
        return Schedule::query()
            ->where('sch_sem_id', $this->schsub_sem_id)
            ->where('sch_is_active', true)
            ->when(
                $this->schsub_fac_id,
                fn($query) => $query->where('sch_fac_id', $this->schsub_fac_id)
            )
            ->whereHas(
                'faculty',
                fn($query) =>
                $query->where('fac_dept_id', $this->schsub_dept_id)
            )
            ->get();
    }
}
