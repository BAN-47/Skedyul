<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model {

    protected $table = 'notification';
    protected $primaryKey = 'notif_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'notif_created_at';
    const UPDATED_AT = 'notif_updated_at';

    protected $fillable = [
        'notif_user_id',
        'notif_title',
        'notif_message',
        'notif_type',
        'notif_is_read',
    ];
}



?>