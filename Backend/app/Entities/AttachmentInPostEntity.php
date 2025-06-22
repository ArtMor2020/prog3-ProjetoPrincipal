<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class AttachmentInPostEntity extends Entity
{
    protected $attributes = [
        'id_attachment' => null,
        'id_post' => null,
    ];

    public function getIdAttachment()
    {
        return $this->attributes['id_attachment'];
    }
    public function setIdAttachment(int $id_attachment)
    {
        $this->attributes['id_attachment'] = $id_attachment;
        return $this;
    }

    public function getIdPost()
    {
        return $this->attributes['id_post'];
    }
    public function setIdPost(int $id_post)
    {
        $this->attributes['id_post'] = $id_post;
        return $this;
    }
}
