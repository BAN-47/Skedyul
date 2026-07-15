<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model {

    protected $table = 'faculty';
    protected $primaryKey = 'fac_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'fac_created_at';
    const UPDATED_AT = 'fac_updated_at';

    protected $fillable = [
        'fac_usr_id',
        'fac_dept_id',
        'fac_employment_type',
        'fac_rank',
        'fac_profile_image'
    ];
};

?>