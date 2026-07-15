<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section_Subject extends Model {

    protected $table = 'section_subject';
    protected $primaryKey = 'ssub_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ssub_sec_id',
        'ssub_subj_id'
    ];
}

?>