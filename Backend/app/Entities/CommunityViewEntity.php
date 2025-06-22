<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CommunityViewEntity extends Entity
{
    protected $attributes = [
        'id_community' => null,
        'id_user' => null,
        'viewed_at' => null,
    ];

    public function getIdCommunity()
    {
        return $this->attributes['id_community'];
    }
    public function setIdCommunity(int $id_community)
    {
        $this->attributes['id_community'] = $id_community;
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

    public function getViewedAt()
    {
        return $this->attributes['viewed_at'];
    }
    public function setViewedAt(string $viewed_at)
    {
        $this->attributes['viewed_at'] = $viewed_at;
        return $this;
    }
}
