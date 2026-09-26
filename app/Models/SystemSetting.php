<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemSetting extends Model
{
    use HasUuids;

    protected $table = 'system_setting';
    protected $primaryKey = 'sset_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['sset_key', 'sset_value', 'sset_updated_by'];

    public static function get(string $key, $default = null)
    {
        $row = static::where('sset_key', $key)->first();
        return $row ? $row->sset_value : $default;
    }

    public static function set(string $key, $value, ?string $updatedBy = null): void
    {
        static::updateOrCreate(
            ['sset_key' => $key],
            [
                'sset_value' => (string) $value,
                'sset_updated_by' => $updatedBy,
                'sset_updated_at' => now(),
            ]
        );
    }
}