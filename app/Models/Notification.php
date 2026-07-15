<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasUuids;

    protected $table = 'notification';
    protected $primaryKey = 'notif_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'notif_usr_id',
        'notif_title',
        'notif_message',
        'notif_type',
        'notif_is_read',
    ];

    protected $casts = [
        'notif_is_read'    => 'boolean',
        'notif_created_at' => 'datetime',
    ];

    const CREATED_AT = 'notif_created_at';
    const UPDATED_AT = 'notif_updated_at';

    public function user()
    {
        return $this->belongsTo(User::class, 'notif_usr_id', 'usr_id');
    }

    // convenience scope
    public function scopeUnread($query)
    {
        return $query->where('notif_is_read', false);
    }
}