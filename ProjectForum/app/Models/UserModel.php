<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\UserEntity;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = UserEntity::class;

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'about',
        'is_private',
        'is_banned',
        'is_deleted'
    ];
}
