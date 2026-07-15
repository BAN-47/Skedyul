<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class, 'fac_usr_id', 'usr_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'fac_dept_id', 'dept_id');
    }
}