<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\BlockedUserEntity;

class BlockedUserModel extends Model
{
    protected $table            = 'blocked_user';
    protected $primaryKey       = ['id_user', 'id_blocked_user'];

    protected $useAutoIncrement = false;
    protected $returnType       = BlockedUserEntity::class;

    protected $allowedFields = [
        'id_user',
        'id_blocked_user',
    ];

}
