<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\NotificationEntity;

class NotificationModel extends Model
{
    protected $table            = 'notification';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = NotificationEntity::class;

    protected $allowedFields = [
        'id_user',
        'status',
        'event_date',
        'type',                       // mention, message, pending_post, friend_request
        'id_origin',
    ];
}
