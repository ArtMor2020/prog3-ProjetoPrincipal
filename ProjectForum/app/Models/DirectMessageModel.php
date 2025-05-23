<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\DirectMessageEntity;

class DirectMessageModel extends Model
{
    protected $table = 'direct_message';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = DirectMessageEntity::class;

    protected $allowedFields = [
        'id_sender',
        'id_reciever',
        'content',
        'sent_at',
        'is_seen',
        'is_deleted',
    ];
}
