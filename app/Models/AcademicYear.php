<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'academic_year';
    protected $primaryKey = 'ay_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'ay_created_at';

    protected $fillable = [
        'ay_academic_year',
        'ay_year_label',
        'ay_is_active'
    ];
}

?>