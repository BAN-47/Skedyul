<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fac_Specialization extends Model {

    protected $table = 'fac_specialization';
    protected $primaryKey = 'fspec_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fspec_fac_id',
        'fspec_specialization'
    ];

}

?>