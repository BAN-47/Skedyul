<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Course extends Model
{
    use HasUuids;

    protected $table = 'course';
    protected $primaryKey = 'course_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'course_created_at';
    const UPDATED_AT = 'course_updated_at';

    protected array $legacyAttributeAliases = [
        'subj_id' => 'course_id',
        'subj_dept_id' => 'course_college_id',
        'subj_prog_id' => 'course_dept_id',
        'subj_code' => 'course_code',
        'subj_name' => 'course_name',
        'subj_units' => 'course_units',
        'subj_lecture_hours' => 'course_lecture_hours',
        'subj_lab_hours' => 'course_lab_hours',
        'subj_is_active' => 'course_is_active',
        'subj_created_at' => 'course_created_at',
        'subj_updated_at' => 'course_updated_at',
        'subj_year_level' => 'course_year_level',
        'subj_semester' => 'course_semester',
    ];

    protected $fillable = [
        'course_college_id',
        'course_dept_id',
        'course_code',
        'course_name',
        'course_units',
        'course_lecture_hours',
        'course_lab_hours',
        'course_year_level',
        'course_semester',
        'course_is_active',
        'subj_dept_id',
        'subj_prog_id',
        'subj_code',
        'subj_name',
        'subj_units',
        'subj_lecture_hours',
        'subj_lab_hours',
        'subj_is_active',
    ];

    public function department()
    {
        return $this->belongsTo(College::class, 'course_college_id', 'college_id');
    }

    public function program()
    {
        return $this->belongsTo(Departments::class, 'course_dept_id', 'dept_id');
    }

    public function studyLoads()
    {
        return $this->hasMany(Study_Load::class, 'sl_course_id', 'course_id');
    }

    public function getAttribute($key)
    {
        return parent::getAttribute($this->legacyAttributeAliases[$key] ?? $key);
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->legacyAttributeAliases[$key] ?? $key, $value);
    }
}
