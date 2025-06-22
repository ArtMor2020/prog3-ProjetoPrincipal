<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserInCommunityEntity extends Entity
{
    protected $attributes = [
        'id_user' => null,
        'id_community' => null,
        'role' => 'member',  // ADMIN, MODERATOR, MEMBER
        'is_banned' => false,
    ];

    public function getIdUser()
    {
        return $this->attributes['id_user'];
    }
    public function setIdUser(int $id_user)
    {
        $this->attributes['id_user'] = $id_user;
        return $this;
    }

    public function getIdCommunity()
    {
        return $this->attributes['id_community'];
    }
    public function setIdCommunity(int $id_community)
    {
        $this->attributes['id_community'] = $id_community;
        return $this;
    }

    public function getRole()
    {
        return $this->attributes['role'];
    }
    public function setRole(string $role)
    {
        $this->attributes['role'] = $role;
        return $this;
    }

    public function getIsBanned()
    {
        return (bool) $this->attributes['is_banned'];
    }
    public function setIsBanned(bool $is_banned)
    {
        $this->attributes['is_banned'] = $is_banned;
        return $this;
    }
}
