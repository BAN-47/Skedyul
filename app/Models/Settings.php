<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'system_setting';
    protected $primaryKey = 'sset_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const UPDATED_AT = 'sset_updated_at';

    protected $fillable = [
        'sset_key',
        'sset_value',
        'sset_updated_by'
    ];
}

?>