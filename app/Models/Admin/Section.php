<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Program;

class Section extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'sec_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'sec_created_at';
    const UPDATED_AT = 'sec_updated_at';

    protected $fillable = [
        'sec_prog_id',
        'sec_ay_id',
        'sec_sem_id',
        'sec_name',
        'sec_year_level',
        'sec_no_of_students',
        'sec_status'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'sec_prog_id', 'prog_id');
    }
}