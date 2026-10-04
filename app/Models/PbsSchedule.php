<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PbsSchedule extends Model
{
    use HasUuids;

    protected $table = 'pbs_schedule';

    protected $primaryKey = 'pbs_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'pbs_created_at';
    const UPDATED_AT = 'pbs_updated_at';

    protected $fillable = [
        'pbs_subj_id',
        'pbs_fac_id',
        'pbs_sec_id',
        'pbs_room_id',
        'pbs_sem_id',
        'pbs_created_by',
        'pbs_day',
        'pbs_start_time',
        'pbs_end_time',
        'pbs_description',
        'pbs_status',
        'pbs_is_active',
    ];

    public function subject()
    {
        return $this->belongsTo(
            Course::class,
            'pbs_subj_id',
            'course_id'
        );
    }

    public function faculty()
    {
        return $this->belongsTo(
            Faculty::class,
            'pbs_fac_id',
            'fac_id'
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'pbs_sec_id',
            'sec_id'
        );
    }

    public function room()
    {
        return $this->belongsTo(
            Room::class,
            'pbs_room_id',
            'room_id'
        );
    }

    public function semester()
    {
        return $this->belongsTo(
            Semester::class,
            'pbs_sem_id',
            'sem_id'
        );
    }
}
