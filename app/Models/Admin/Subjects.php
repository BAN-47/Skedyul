<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'subject';
    protected $primaryKey = 'subj_id';

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
}
