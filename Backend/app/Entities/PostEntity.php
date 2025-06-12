<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PostEntity extends Entity
{
    protected $attributes = [
        'id'           => null,
        'id_user'      => null,
        'id_community' => null,
        'title'        => null,
        'description'  => null,
        'posted_at'    => null,
        'updated_at'   => null,
        'is_approved'  => false,
        'is_deleted'   => false,
    ];

    public function getId() {
        return $this->attributes['id']; }
    public function setId(int $id) {
        $this->attributes['id'] = $id;
        return $this; }

    public function getIdUser() {
        return $this->attributes['id_user']; }
    public function setIdUser(int $id_user) {
        $this->attributes['id_user'] = $id_user;
        return $this; }

    public function getIdCommunity() {
        return $this->attributes['id_community']; }
    public function setIdCommunity(int $id_community) {
        $this->attributes['id_community'] = $id_community;
        return $this; }

    public function getTitle() {
        return $this->attributes['title']; }
    public function setTitle(string $title) {
        $this->attributes['title'] = $title;
        return $this; }

    public function getDescription() {
        return $this->attributes['description']; }
    public function setDescription(string $description) {
        $this->attributes['description'] = $description;
        return $this; }

    public function getPostedAt() {
        return $this->attributes['posted_at']; }
    public function setPostedAt(string $posted_at) {
        $this->attributes['posted_at'] = $posted_at;
        return $this; }

    public function getUpdatedAt() {
        return $this->attributes['updated_at']; }
    public function setUpdatedAt(string $updated_at) {
        $this->attributes['updated_at'] = $updated_at;
        return $this; }

    public function getIsApproved() {
        return $this->attributes['is_approved']; }
    public function setIsApproved(bool $is_approved) {
        $this->attributes['is_approved'] = $is_approved;
        return $this; }

    public function getIsDeleted() {
        return $this->attributes['is_deleted']; }
    public function setIsDeleted(bool $is_deleted) {
        $this->attributes['is_deleted'] = $is_deleted;
        return $this; }
}
