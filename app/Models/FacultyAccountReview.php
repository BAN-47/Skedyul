<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyAccountReview extends Model
{
    protected $table = 'faculty_account_reviews';
    protected $primaryKey = 'fvr_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fvr_id',
        'fvr_usr_id',
        'fvr_id_photo_path',
        'fvr_status',
        'fvr_applicant_name',
        'fvr_applicant_email',
        'fvr_decision_note',
        'fvr_reviewed_by',
        'fvr_reviewed_at',
    ];

    protected $casts = [
        'fvr_reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'fvr_usr_id', 'usr_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'fvr_reviewed_by', 'usr_id');
    }
}
