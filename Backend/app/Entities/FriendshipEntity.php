<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class FriendshipEntity extends Entity
{
    protected $attributes = [
        'id_user1' => null,    // who makes the request
        'id_user2' => null,    // who recieves the request
        'status' => null,    // 'friends' or 'friend_request'
        'requested_at' => null,
        'friends_since' => null,
    ];

    public function getIdUser1()
    {
        return $this->attributes['id_user1'];
    }
    public function setIdUser1(int $id)
    {
        $this->attributes['id_user1'] = $id;
        return $this;
    }

    public function getIdUser2()
    {
        return $this->attributes['id_user2'];
    }

    public function setIdUser2(int $id)
    {
        $this->attributes['id_user2'] = $id;
        return $this;
    }

    public function getStatus()
    {
        return $this->attributes['status'];
    }

    public function setStatus(string $status)
    {
        $this->attributes['status'] = $status;
        return $this;
    }

    public function getRequestedAt()
    {
        return $this->attributes['requested_at'];
    }

    public function setRequestedAt(string $datetime)
    {
        $this->attributes['requested_at'] = $datetime;
        return $this;
    }

    public function getFriendsSince()
    {
        return $this->attributes['friends_since'];
    }

    public function setFriendsSince(?string $datetime)
    {
        $this->attributes['friends_since'] = $datetime;
        return $this;
    }
}