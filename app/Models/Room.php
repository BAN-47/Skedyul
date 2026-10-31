<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Room extends Model
{
    use HasUuids;

    protected $table = 'room';
    protected $primaryKey = 'room_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'room_created_at';
    const UPDATED_AT = 'room_updated_at';

    protected $fillable = [
        'room_name',
        'room_college_id',
        'room_building',
        'room_type',
        'room_capacity',
        'room_is_available',
    ];

    protected $casts = [
        'room_is_available' => 'boolean',
    ];

    public function getRoomLocationLabelAttribute(): ?string
    {
        if (!preg_match('/(\d{2,})\s*$/', (string) $this->room_name, $matches)) {
            return null;
        }

        $number = $matches[1];
        $floor = (int) substr($number, 0, 1);
        $roomNumber = substr($number, 1);
        $suffix = match ($floor % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };

        return "{$floor}{$suffix} Floor, Room {$roomNumber}";
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'sch_room_id', 'room_id');
    }
}
