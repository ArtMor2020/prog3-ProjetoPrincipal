<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CommunityEntity extends Entity
{
    protected $attributes = [
        'id'          => null,
        'name'        => null,
        'description' => null,
        'id_owner'    => null,
        'is_private'  => false,
        'is_deleted'  => false,
        'is_banned'   => false,
    ];

    public function getId() {
        return $this->attributes['id']; }
    public function setId(int $id) {
        $this->attributes['id'] = $id;
        return $this; }

    public function getName() {
        return $this->attributes['name']; }
    public function setName(string $name) {
        $this->attributes['name'] = $name;
        return $this; }

    public function getDescription() {
        return $this->attributes['description']; }
    public function setDescription(string $description) {
        $this->attributes['description'] = $description;
        return $this; }

    public function getIdOwner() {
        return $this->attributes['id_owner']; }
    public function setIdOwner(int $id_owner) {
        $this->attributes['id_owner'] = $id_owner;
        return $this; }

    public function getIsPrivate() {
        return $this->attributes['is_private']; }
    public function setIsPrivate(bool $is_private) {
        $this->attributes['is_private'] = $is_private;
        return $this; }

    public function getIsDeleted() {
        return $this->attributes['is_deleted']; }
    public function setIsDeleted(bool $is_deleted) {
        $this->attributes['is_deleted'] = $is_deleted;
        return $this; }

    public function getIsBanned() {
        return $this->attributes['is_banned']; }
    public function setIsBanned(bool $is_banned) {
        $this->attributes['is_banned'] = $is_banned;
        return $this; }
}
