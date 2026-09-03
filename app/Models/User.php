<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'usr_email',
        'usr_password_hash',
        'usr_role',
        'usr_is_active',
        'usr_bio',
    ];

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

    public function faculty()
    {
        return $this->hasOne(Faculty::class, 'fac_usr_id', 'usr_id');
    }

    public function dean()
    {
        return $this->hasOne(Dean::class, 'dean_usr_id', 'usr_id');
    }

    public function deptChairRecord()
    {
        return $this->hasOne(
            Dept_Chair::class,
            'dc_usr_id',
            'usr_id'
        );
    }

    public function profile()
    {
        return match ($this->usr_role) {
            'faculty' => $this->faculty,
            'dean' => $this->dean,
            'department_chair' => $this->deptChairRecord,
            default => null,
        };
    }

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
                ])->filter()->implode(', ');
            })
            ->filter()
            ->unique()
            ->values();

        return $rooms?->implode('; ');
    }
}