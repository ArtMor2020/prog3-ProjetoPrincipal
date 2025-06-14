<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RatingInCommentEntity extends Entity
{
    protected $attributes = [
        'id_comment' => null,
        'id_user'    => null,
        'is_upvote'  => true,
    ];

    public function getIdComment() {
        return $this->attributes['id_comment']; }
    public function setIdComment(int $id_comment) {
        $this->attributes['id_comment'] = $id_comment;
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
