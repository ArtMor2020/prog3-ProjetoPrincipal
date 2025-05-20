<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Entities\AttachmentInPostEntity;

class AttachmentInPostModel extends Model
{
    protected $table            = 'attachment_in_post';
    protected $primaryKey       = ['id_attachment', 'id_post'];

    protected $useAutoIncrement = false;
    protected $returnType       = AttachmentInPostEntity::class;

    protected $allowedFields = [
        'id_attachment',
        'id_post',
    ];
}
