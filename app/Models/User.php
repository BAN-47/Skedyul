<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $table = 'USER';

    protected $primaryKey = 'usr_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'usr_id',
        'usr_name',
        'usr_first_name',
        'usr_middle_name',
        'usr_last_name',
        'usr_suffix',
        'usr_email',
        'usr_password_hash',
        'usr_role',
        'usr_is_active',
        'usr_employee_id',
        'usr_rank_title',
        'usr_gender',
        'usr_civil_status',
        'usr_dob',
        'usr_nationality',
    ];

    /**
     * USER.usr_id is a UUID PK (non-incrementing). Generate it on create
     * so role profiles (faculty.fac_usr_id, etc.) always get a real value.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $user) {
            if (empty($user->usr_id)) {
                $user->usr_id = (string) Str::uuid();
            }
        });
    }

    protected $hidden = [
        'usr_password_hash',
    ];

    protected $casts = [
        'usr_is_active' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->usr_password_hash;
    }

    /*
    |--------------------------------------------------------------------------
    | Role Profiles
    |--------------------------------------------------------------------------
    */

    public function faculty()
    {
        return $this->hasOne(
            Faculty::class,
            'fac_usr_id',
            'usr_id'
        );
    }

    public function dean()
    {
        return $this->hasOne(
            Dean::class,
            'dean_usr_id',
            'usr_id'
        );
    }

    public function deptChair()
    {
        return $this->hasOne(
            Dept_Chair::class,
            'dc_usr_id',
            'usr_id'
        );
    }

    public function deptChairRecord()
    {
        return $this->hasOne(
            Dept_Chair::class,
            'dc_usr_id',
            'usr_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get the correct profile based on the user's role
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        return match ($this->usr_role) {

            'faculty' =>
                $this->faculty,

            'dean' =>
                $this->dean,

            'department_chair' =>
                $this->deptChair,

            default =>
                null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Faculty Room Location
    |--------------------------------------------------------------------------
    */

    public function getRoomLocationAttribute(): ?string
    {
        $rooms = $this->faculty?->studyLoads
            ->map(fn ($load) => $load->schedule?->room)
            ->filter()
            ->map(function ($room) {

                return collect([
                    $room->room_name,
                    $room->room_building,
                    $room->room_location,
                ])
                    ->filter()
                    ->implode(', ');
            })
            ->filter()
            ->unique()
            ->values();

        return $rooms?->implode('; ');
    }
}