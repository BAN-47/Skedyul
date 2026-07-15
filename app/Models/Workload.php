<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workload extends Model {

    protected $table = 'workload';
    protected $primaryKey = 'wl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'wl_created_at';
    const UPDATED_AT = 'wl_updated_at';

    protected $fillable = [
        'wl_fac_id',
        'wl_sem_id',
        'wl_ay_id',
        'wl_type',
        'wl_total_hours'
    ];
}

?>