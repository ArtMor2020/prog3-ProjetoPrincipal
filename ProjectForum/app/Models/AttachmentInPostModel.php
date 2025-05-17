<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentInPostModel extends Model
{
    private int $IdPost = 0;
    private int $IdAttachment = 0;

    public function getIdPost(): int { return $this->IdPost; }
    public function setIdPost(int $idPost): void { $this->IdPost = $idPost; }

    public function getIdAttachment(): int { return $this->IdAttachment; }
    public function setIdAttachment(int $idAttachment): void { $this->IdAttachment = $idAttachment; }
}
