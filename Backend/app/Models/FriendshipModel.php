<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\FriendshipEntity;

class FriendshipModel extends Model
{
    protected $table = 'friendship';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = FriendshipEntity::class;

    protected $allowedFields = [
        'id_user1',
        'id_user2',
        'status',
        'requested_at',
        'friends_since',
    ];
}