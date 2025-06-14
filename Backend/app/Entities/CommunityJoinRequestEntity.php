<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CommunityJoinRequestEntity extends Entity
{
    protected $attributes = [
        'id_community' => null,
        'id_user'      => null,
        'requested_at' => null,
        'status'       => null, // 'pending', 'approved', 'rejected', 'invited'?
    ];

    public function getIdCommunity() {
        return $this->attributes['id_community']; }
    public function setIdCommunity(int $id_community) {
        $this->attributes['id_community'] = $id_community;
        return $this; }

    public function getIdUser() {
        return $this->attributes['id_user']; }
    public function setIdUser(int $id_user) {
        $this->attributes['id_user'] = $id_user;
        return $this; }

    public function getRequestedAt() {
        return $this->attributes['requested_at']; }
    public function setRequestedAt(string $requested_at) {
        $this->attributes['requested_at'] = $requested_at;
        return $this; }

    public function getStatus() {
        return $this->attributes['status']; }
    public function setStatus(string $status) {
        $this->attributes['status'] = $status;
        return $this; }
}
