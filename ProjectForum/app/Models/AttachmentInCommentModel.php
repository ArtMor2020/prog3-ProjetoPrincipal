<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\AttachmentInCommentEntity;

class AttachmentInCommentModel extends Model
{
    protected $table            = 'attachment_in_comment';
    protected $primaryKey       = ['id_attachment', 'id_comment'];

    protected $useAutoIncrement = false;
    protected $returnType       = AttachmentInCommentEntity::class;

    protected $allowedFields = [
        'id_attachment',
        'id_comment',
    ];
}
