<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dean extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * PostgreSQL converts unquoted table names to lowercase, so we use 'dean'.
     *
     * @var string
     */
    protected $table = 'dean';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'dean_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dean_usr_id',
        'dean_dept_id',
        'dean_first_name',
        'dean_middle_name',
        'dean_last_name',
        'dean_suffix',
        'dean_phone_number',
        'dean_gmail',
        'dean_address',
        'dean_profile_image',
        'dean_assigned_at',
    ];

    /**
     * Get the user account associated with the Dean.
     */
    public function user()
    {
        // Foreign keys must also be lowercase to match PostgreSQL's storage
        return $this->belongsTo(User::class, 'dean_usr_id', 'usr_id');
    }

    /**
     * Get the department overseen by the Dean.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dean_dept_id', 'dept_id');
    }
}