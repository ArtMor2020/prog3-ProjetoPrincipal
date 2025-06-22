<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class AttachmentEntity extends Entity
{
    protected $attributes = [
        'id' => null,
        'type' => null,   // IMAGE, VIDEO, GIF, DOCUMENT, ZIP, OTHER
        'path' => null,
        'is_deleted' => false,
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }
    public function setId(int $id)
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function getType()
    {
        return $this->attributes['type'];
    }
    public function setType(string $type)
    {
        $this->attributes['type'] = $type;
        return $this;
    }

    public function getPath()
    {
        return $this->attributes['path'];
    }
    public function setPath(string $path)
    {
        $this->attributes['path'] = $path;
        return $this;
    }

    public function getIsDeleted()
    {
        return $this->attributes['is_deleted'];
    }
    public function setIsDeleted(bool $is_deleted)
    {
        $this->attributes['is_deleted'] = $is_deleted;
        return $this;
    }
}
