<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class room extends Model
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
        'room_type',
        'room_capacity',
        'room_is_available',
        'room_building',
        'room_location',
    ];
    
    protected $casts = [
        'room_is_available' => 'boolean',
    ];
    
}

?>