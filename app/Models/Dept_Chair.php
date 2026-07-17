<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept_Chair extends Model
{
    protected $table = 'department_chair';
    protected $primaryKey = 'dc_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dc_assigned_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'dc_usr_id', 'dc_dept_id', 'dc_first_name', 'dc_middle_name',
        'dc_last_name', 'dc_suffix', 'dc_phone_number', 'dc_gmail',
        'dc_address', 'dc_profile_image',
    ];

    public function user()       { return $this->belongsTo(User::class, 'dc_usr_id', 'usr_id'); }
    public function department() { return $this->belongsTo(Department::class, 'dc_dept_id', 'dept_id'); }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->dc_first_name, $this->dc_middle_name, $this->dc_last_name, $this->dc_suffix]);
        return implode(' ', $parts);
    }
}