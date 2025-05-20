<?php
namespace App\Models;

use CodeIgniter\Model;
use app\Entities\PostEntity;

class PostModel extends Model
{
    protected $table         = 'post';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = PostEntity::class;

    protected $allowedFields = [
        'id_user',
        'id_community',
        'title',
        'description',
        'posted_at',
        'updated_at',
        'is_approved',
        'is_deleted',
    ];
}
