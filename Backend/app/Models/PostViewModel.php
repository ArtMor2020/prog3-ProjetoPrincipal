<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PostViewEntity;

class PostViewModel extends Model
{
    protected $table = 'post_view';
    protected $primaryKey = ['id_post', 'id_user'];
    protected $useAutoIncrement = false;
    protected $returnType = PostViewEntity::class;

    protected $allowedFields = [
        'id_post',
        'id_user',
        'viewed_at',
    ];

    protected $useTimestamps = false;
}

