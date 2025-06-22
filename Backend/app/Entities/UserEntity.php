<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $attributes = [
        'id' => null,
        'name' => null,
        'email' => null,
        'password' => null,
        'about' => null,
        'is_private' => false,
        'is_banned' => false,
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

    public function getName()
    {
        return $this->attributes['name'];
    }
    public function setName(string $name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->attributes['email'];
    }
    public function setEmail(string $email)
    {
        $this->attributes['email'] = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->attributes['password'];
    }
    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function getAbout()
    {
        return $this->attributes['about'];
    }
    public function setAbout(?string $about)
    {
        $this->attributes['about'] = $about;
        return $this;
    }

    public function getIsPrivate()
    {
        return $this->attributes['is_private'];
    }
    public function setIsPrivate(bool $is_private)
    {
        $this->attributes['is_private'] = $is_private;
        return $this;
    }

    public function getIsBanned()
    {
        return $this->attributes['is_banned'];
    }
    public function setIsBanned(bool $is_banned)
    {
        $this->attributes['is_banned'] = $is_banned;
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
