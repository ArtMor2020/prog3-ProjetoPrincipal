<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CommentEntity extends Entity
{
    protected $attributes = [
        'id' => null,
        'id_user' => null,
        'id_parent_post' => null,
        'id_parent_comment' => null,
        'content' => null,
        'is_deleted' => false,
        'created_at' => null,
        'updated_at' => null,
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

    public function getIdUser()
    {
        return $this->attributes['id_user'];
    }
    public function setIdUser(int $id_user)
    {
        $this->attributes['id_user'] = $id_user;
        return $this;
    }

    public function getIdParentPost()
    {
        return $this->attributes['id_parent_post'];
    }
    public function setIdParentPost(int $id_parent_post)
    {
        $this->attributes['id_parent_post'] = $id_parent_post;
        return $this;
    }

    public function getIdParentComment()
    {
        return $this->attributes['id_parent_comment'];
    }
    public function setIdParentComment(?int $id_parent_comment)
    {
        $this->attributes['id_parent_comment'] = $id_parent_comment;
        return $this;
    }

    public function getContent()
    {
        return $this->attributes['content'];
    }
    public function setContent(string $content)
    {
        $this->attributes['content'] = $content;
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

    public function getCreatedAt()
    {
        return $this->attributes['created_at'];
    }
    public function setCreatedAt(string $created_at)
    {
        $this->attributes['created_at'] = $created_at;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->attributes['updated_at'];
    }
    public function setUpdatedAt(?string $updated_at)
    {
        $this->attributes['updated_at'] = $updated_at;
        return $this;
    }
}
