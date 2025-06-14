<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\BlockedUserEntity;

class BlockedUserModel extends Model
{
    protected $table = 'blocked_user';
    // Composite primary key: note array syntax is supported in CI4.6+
    protected $primaryKey = ['id_user', 'id_blocked_user'];
    // No auto-increment on a composite key
    protected $useAutoIncrement = false;

    // We want raw arrays back for pivot entries
    protected $returnType = 'array';

    // Exactly the two columns we will insert
    protected $allowedFields = [
        'id_user',
        'id_blocked_user',
    ];

    // No timestamps on pivot
    protected $useTimestamps = false;
}
