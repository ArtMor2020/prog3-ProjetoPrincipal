<?php
namespace App\Models;

use CodeIgniter\Model;
use app\Entities\CommentEntity;

class CommentModel extends Model
{
    protected $table            = 'comment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = CommentEntity::class;

    protected $allowedFields = [
        'id_user',
        'id_parent_post',
        'id_parent_comment',
        'content',
        'is_deleted',
        'created_at',
        'updated_at',
    ];
}
