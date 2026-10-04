<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class College extends Model
{
    use HasUuids;

    protected $table = 'college';
    protected $primaryKey = 'college_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only dept_created_at exists

    protected array $legacyAttributeAliases = [
        'dept_id' => 'college_id',
        'dept_name' => 'college_name',
        'dept_code' => 'college_code',
        'dept_created_at' => 'college_created_at',
    ];

    protected $fillable = [
        'college_name',
        'college_code',
    ];

    public function programs()
    {
        return $this->hasMany(Departments::class, 'dept_college_id', 'college_id');
    }

    public function faculty()
    {
        return $this->hasMany(Faculty::class, 'fac_college_id', 'college_id');
    }

    public function subjects()
    {
        return $this->hasMany(Course::class, 'course_college_id', 'college_id');
    }

    public function deptChairs()
    {
        return $this->hasMany(Dept_Chair::class, 'dc_college_id', 'college_id');
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