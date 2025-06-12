<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RatingInPostEntity extends Entity
{
    protected $attributes = [
        'id_post'   => null,
        'id_user'   => null,
        'is_upvote' => true,
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

    public function getIsUpvote() {
        return (bool) $this->attributes['is_upvote']; }
    public function setIsUpvote(bool $is_upvote) {
        $this->attributes['is_upvote'] = $is_upvote;
        return $this; }
}
