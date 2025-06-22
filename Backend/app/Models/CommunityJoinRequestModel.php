<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\CommunityJoinRequestEntity;

class CommunityJoinRequestModel extends Model
{
    protected $table = 'community_join_request';

    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    // --------------------------------

    protected $returnType = CommunityJoinRequestEntity::class;

    protected $allowedFields = [
        'id_community',
        'id_user',
        'requested_at',
        'status',
    ];
}