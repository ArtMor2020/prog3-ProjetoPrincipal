<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PostViewEntity extends Entity
{
    protected $attributes = [
        'id_post'   => null,
        'id_user'   => null,
        'viewed_at' => null,
    ];

    public function getIdPost() {
        return $this->attributes['id_post']; }
    public function setIdPost(int $id_post) {
        $this->attributes['id_post'] = $id_post;
        return $this; }

    public function getIdUser() {
        return $this->attributes['id_user']; }
    public function setIdUser(int $id_user) {
        $this->attributes['id_user'] = $id_user;
        return $this; }

    public function getViewedAt() {
        return $this->attributes['viewed_at']; }
    public function setViewedAt(string $viewed_at) {
        $this->attributes['viewed_at'] = $viewed_at;
        return $this; }
}
