<?php 

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\UserInCommunityEntity;

class UserInCommunityModel extends Model
{
    protected $table = 'user_in_community';
    protected $primaryKey = ['id_user','id_community'];
    protected $useAutoIncrement = false;
    protected $returnType = UserInCommunityEntity::class;

    protected $allowedFields = [
        'id_user',
        'id_community',
        'role',
        'is_banned',
    ];
}
