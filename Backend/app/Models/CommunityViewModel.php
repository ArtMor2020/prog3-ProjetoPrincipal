<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\CommunityViewEntity;

class CommunityViewModel extends Model
{
    protected $table = 'community_view';
    protected $primaryKey = ['id_community', 'id_user'];
    protected $useAutoIncrement = false;
    protected $returnType = CommunityViewEntity::class;

    protected $allowedFields = [
        'id_community',
        'id_user',
        'viewed_at',
    ];
}
