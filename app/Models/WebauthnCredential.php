<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebauthnCredential extends Model
{
    use HasUuids;

    protected $table = 'webauthn_credentials';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    // The table has created_at but no updated_at column.
    public $timestamps = false;

    protected $fillable = [
        'usr_id',
        'credential_id',
        'public_key',
        'sign_count',
        'transports',
        'device_name',
        'credential_record',
        'last_used_at',
    ];

    protected $casts = [
        'sign_count' => 'integer',
        'transports' => 'array',
        'credential_record' => 'array',
        'created_at' => 'immutable_datetime',
        'last_used_at' => 'immutable_datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id', 'usr_id');
    }
}
