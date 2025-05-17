<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentInCommentModel extends Model
{
    private int $IdComment = 0;
    private int $IdAttachment = 0;

    public function getIdComment(): int { return $this->IdComment; }
    public function setIdComment(int $idComment): void { $this->IdComment = $idComment; }

    public function getIdAttachment(): int { return $this->IdAttachment; }
    public function setIdAttachment(int $idAttachment): void { $this->IdAttachment = $idAttachment; }
}
