<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\UserEntity;

class UserModel extends Model
{
    protected $table = 'user';
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
