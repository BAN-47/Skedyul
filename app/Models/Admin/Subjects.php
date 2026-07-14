<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'subject';
    protected $primaryKey = 'subj_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'subj_created_at';
    const UPDATED_AT = 'subj_updated_at';

    protected $fillable = [
        'subj_dept_id',
        'subj_prog_id',
        'subj_code',
        'subj_name',
        'subj_lecture_hours',
        'subj_lab_hours',
        'subj_is_active'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'subj_dept_id', 'dept_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'subj_prog_id', 'prog_id');
    }
}
