<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\CommunityJoinRequestEntity;

class CommunityJoinRequestModel extends Model
{
    protected $table            = 'community_join_request';
    protected $primaryKey       = ['id_community', 'id_user'];
    protected $useAutoIncrement = false;
    protected $returnType       = CommunityJoinRequestEntity::class;

    protected $allowedFields = [
        'id_community',
        'id_user',
        'requested_at',
        'status',
    ];
}
