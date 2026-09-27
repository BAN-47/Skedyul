<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dean extends Model
{
    use HasFactory;

    protected $table = 'dean';

    protected $primaryKey = 'dean_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'dean_usr_id',

        // Department / Program
        'dean_dept_id',
        'dean_prog_id',

        // Personal Information
        'dean_first_name',
        'dean_middle_name',
        'dean_last_name',
        'dean_suffix',
        'dean_employee_id',
        'dean_gender',
        'dean_civil_status',
        'dean_dob',
        'dean_nationality',

        // Contact Information
        'dean_phone_number',
        'dean_gmail',
        'dean_address',

        // Profile
        'dean_profile_image',

        // Assignment
        'dean_assigned_at',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'dean_usr_id',
            'usr_id'
        );
    }

    public function department()
    {
        return $this->belongsTo(
            Department::class,
            'dean_dept_id',
            'dept_id'
        );
    }

    public function program()
    {
        return $this->belongsTo(
            Program::class,
            'dean_prog_id',
            'prog_id'
        );
    }
}