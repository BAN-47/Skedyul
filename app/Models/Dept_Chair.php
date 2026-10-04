<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Dept_Chair extends Model
{
    use HasUuids;

    protected $table = 'department_chair';
    protected $primaryKey = 'dc_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // only dc_assigned_at exists

    protected array $legacyAttributeAliases = [
        'dc_prog_id' => 'dc_dept_id',
    ];

    protected $fillable = [
        'dc_usr_id',

        // Department / Program
        'dc_college_id',
        'dc_dept_id',
        'dc_prog_id',

        // Personal Information
        'dc_first_name',
        'dc_middle_name',
        'dc_last_name',
        'dc_suffix',
        'dc_employee_id',
        'dc_gender',
        'dc_civil_status',
        'dc_dob',
        'dc_nationality',

        // Contact Information
        'dc_phone_number',
        'dc_address',

        // Profile
        'dc_profile_image',
        'dc_bio',
    ];

    protected $casts = [
        'dc_dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dc_usr_id', 'usr_id');
    }

    public function department()
    {
        return $this->belongsTo(College::class, 'dc_college_id', 'college_id');
    }

    public function program()
    {
        return $this->belongsTo(Departments::class, 'dc_dept_id', 'dept_id');
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->dc_first_name, $this->dc_middle_name, $this->dc_last_name, $this->dc_suffix]);
        return implode(' ', $parts);
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