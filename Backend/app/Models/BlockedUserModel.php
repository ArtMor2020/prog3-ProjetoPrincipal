<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockedUserModel extends Model
{
    protected $table = 'blocked_user';

    protected $primaryKey = ['id_user', 'id_blocked_user'];

    protected $useAutoIncrement = false;
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_user',
        'id_blocked_user',
    ];

    protected $useTimestamps = false;
}