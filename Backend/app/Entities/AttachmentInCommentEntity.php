<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class AttachmentInCommentEntity extends Entity
{
    protected $attributes = [
        'id_attachment' => null,
        'id_comment'    => null,
    ];

    public function getIdAttachment() {
        return $this->attributes['id_attachment']; }
    public function setIdAttachment(int $id_attachment) {
        $this->attributes['id_attachment'] = $id_attachment;
        return $this; }

    public function getIdComment() {
        return $this->attributes['id_comment']; }
    public function setIdComment(int $id_comment) {
        $this->attributes['id_comment'] = $id_comment;
        return $this; }
}
