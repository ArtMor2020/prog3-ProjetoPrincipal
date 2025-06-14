<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\AttachmentEntity;

class AttachmentModel extends Model
{
    protected $table = 'attachment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = AttachmentEntity::class;

    protected $allowedFields = [
        'type',                           // IMAGE, VIDEO, GIF, DOCUMENT, ZIP, OTHER
        'path',
        'is_deleted',
    ];
}
