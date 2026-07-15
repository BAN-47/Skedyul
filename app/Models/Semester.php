<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'sem_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'sem_created_at';

    protected $fillable = [
        'sem_ay_id',
        'sem_name',
        'sem_start_date',
        'sem_end_date',
        'sem_is_active'
    ];
}

?>
