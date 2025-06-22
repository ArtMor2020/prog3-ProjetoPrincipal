<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BlockedUserEntity extends Entity
{
    protected $attributes = [
        'id_user' => null,
        'id_blocked_user' => null,
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

    public function getIdBlockedUser()
    {
        return $this->attributes['id_blocked_user'];
    }
    public function setIdBlockedUser(int $id_blocked_user)
    {
        $this->attributes['id_blocked_user'] = $id_blocked_user;
        return $this;
    }
}
