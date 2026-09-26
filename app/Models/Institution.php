<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $table = 'institution';

    protected $primaryKey = 'inst_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'inst_created_at';
    const UPDATED_AT = 'inst_updated_at';

    protected $fillable = [
        'inst_id',
        'inst_name',
        'inst_branch_campus',
        'inst_college',
        'inst_abbreviation',
        'inst_contact_email',
        'inst_phone',
    ];
}