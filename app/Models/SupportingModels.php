<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Four small models used by Schedule's relations (subject/section/room/
| semester). If you already have any of these, skip that one — only add
| what's missing. Split into separate files if your project convention
| requires one class per file.
*/

class Subject extends Model
{
    protected $table = 'course';
    protected $primaryKey = 'course_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected array $legacyAttributeAliases = [
        'subj_id' => 'course_id',
        'subj_dept_id' => 'course_college_id',
        'subj_prog_id' => 'course_dept_id',
        'subj_code' => 'course_code',
        'subj_name' => 'course_name',
        'subj_lecture_hours' => 'course_lecture_hours',
        'subj_lab_hours' => 'course_lab_hours',
        'subj_is_active' => 'course_is_active',
    ];

    protected $fillable = [
        'course_college_id', 'course_dept_id', 'course_code', 'course_name',
        'course_lecture_hours', 'course_lab_hours', 'course_is_active',
        'subj_dept_id', 'subj_prog_id', 'subj_code', 'subj_name',
        'subj_lecture_hours', 'subj_lab_hours', 'subj_is_active',
    ];

    public function getAttribute($key)
    {
        return parent::getAttribute($this->legacyAttributeAliases[$key] ?? $key);
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->legacyAttributeAliases[$key] ?? $key, $value);
    }
}

class Section extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'sec_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'sec_prog_id', 'sec_ay_id', 'sec_sem_id', 'sec_name',
        'sec_year_level', 'sec_no_of_student', 'sec_max_capacity', 'sec_status',
    ];
}

class Room extends Model
{
    protected $table = 'room';
    protected $primaryKey = 'room_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'room_name', 'room_building', 'room_location', 'room_type',
        'room_capacity', 'room_is_available',
    ];
}

class Semester extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'sem_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'sem_ay_id', 'sem_name', 'sem_start_date', 'sem_end_date', 'sem_is_active',
    ];
}