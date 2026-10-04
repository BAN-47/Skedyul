<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Departments extends Model
{
    use HasUuids;

    protected $table = 'department';
    protected $primaryKey = 'dept_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only prog_created_at exists

    protected array $legacyAttributeAliases = [
        'prog_id' => 'dept_id',
        'prog_dept_id' => 'dept_college_id',
        'prog_name' => 'dept_name',
        'prog_code' => 'dept_code',
        'prog_created_at' => 'dept_created_at',
    ];

    protected $fillable = [
        'dept_college_id',
        'dept_name',
        'dept_code',
    ];

    public function department()
    {
        return $this->belongsTo(College::class, 'dept_college_id', 'college_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'sec_dept_id', 'dept_id');
    }

    public function subjects()
    {
        return $this->hasMany(Course::class, 'course_dept_id', 'dept_id');
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