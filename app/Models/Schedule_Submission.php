<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule_Submission extends Model {

    protected $table = 'schedule_submission';
    protected $primaryKey = 'schsub_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'schsub_dept_id',
        'schsub_sem_id',
        'schsub_submitted_by',
        'schsub_submitted_at',
        'schsub_reviewed_by',
        'schsub_reviewed_at',
        'schsub_status',
        'schsub_remarks'
    ];

    public function department() { return $this->belongsTo(Department::class, 'schsub_dept_id', 'dept_id'); }
}

?>