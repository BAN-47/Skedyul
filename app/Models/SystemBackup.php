<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemBackup extends Model
{
    use HasUuids;

    protected $table = 'system_backup';
    protected $primaryKey = 'bkp_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'bkp_file_path',
        'bkp_status',
        'bkp_error_message',
        'bkp_ran_at',
    ];

    protected $casts = [
        'bkp_ran_at' => 'datetime',
    ];
}