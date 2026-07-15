<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';

    protected $primaryKey = 'prog_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'prog_created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'prog_dept_id',
        'prog_name',
        'prog_code'
    ];
}

?>
