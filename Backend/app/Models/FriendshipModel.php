<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\FriendshipEntity;

class FriendshipModel extends Model
{
    protected $table = 'friendship';
    protected $primaryKey = ['id_user1', 'id_user2'];

    protected $useAutoIncrement = false;
    protected $returnType = FriendshipEntity::class;

    protected $allowedFields = [
        'id_user1',     // who makes the request
        'id_user2',     // who recieves the request
        'status',       // 'friends' or 'friend_request'
        'requested_at',
        'friends_since',
    ];
}