<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\CommunityEntity;

class CommunityModel extends Model
{
    protected $table = 'community';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = CommunityEntity::class;

    protected $allowedFields = [
        'name',
        'description',
        'id_owner',
        'is_private',
        'is_deleted',
        'is_banned',
    ];
}
