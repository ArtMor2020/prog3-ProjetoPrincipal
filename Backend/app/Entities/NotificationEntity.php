<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class NotificationEntity extends Entity
{
    protected $attributes = [
        'id'         => null,
        'id_user'    => null,
        'status'     => null, // 'seen' or 'not_seen'
        'event_date' => null,
        'type'       => null, // mention, message, pending_post, friend_request
        'id_origin'  => null, // id of post, comment, person messaging, community with pending post, etc
    ];

    public function getId() {
        return $this->attributes['id'];
    }
    public function setId(int $id) {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function getIdUser() {
        return $this->attributes['id_user'];
    }
    public function setIdUser(int $id_user) {
        $this->attributes['id_user'] = $id_user;
        return $this;
    }

    public function getStatus() {
        return $this->attributes['status'];
    }
    public function setStatus(string $status) {
        $this->attributes['status'] = $status;
        return $this;
    }

    public function getEventDate() {
        return $this->attributes['event_date'];
    }
    public function setEventDate(string $event_date) {
        $this->attributes['event_date'] = $event_date;
        return $this;
    }

    public function getType() {
        return $this->attributes['type'];
    }
    public function setType(string $type) {
        $this->attributes['type'] = $type;
        return $this;
    }

    public function getIdOrigin() {
        return $this->attributes['id_origin'];
    }
    public function setIdOrigin(int $id_origin) {
        $this->attributes['id_origin'] = $id_origin;
        return $this;
    }
}
