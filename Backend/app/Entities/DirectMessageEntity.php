<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class DirectMessageEntity extends Entity
{
    protected $attributes = [
        'id'         => null,
        'id_sender'  => null,
        'id_reciever'=> null,
        'content'    => null,
        'sent_at'    => null,
        'is_seen'    => false,
        'is_deleted' => false,
    ];

    public function getId() {
        return $this->attributes['id']; }
    public function setId(int $id) {
        $this->attributes['id'] = $id;
        return $this; }

    public function getIdSender() {
        return $this->attributes['id_sender']; }
    public function setIdSender(int $id_sender) {
        $this->attributes['id_sender'] = $id_sender;
        return $this; }

    public function getIdReciever() {
        return $this->attributes['id_reciever']; }
    public function setIdReciever(int $id_reciever) {
        $this->attributes['id_reciever'] = $id_reciever;
        return $this; }

    public function getContent() {
        return $this->attributes['content']; }
    public function setContent(string $content) {
        $this->attributes['content'] = $content;
        return $this; }

    public function getSentAt() {
        return $this->attributes['sent_at']; }
    public function setSentAt(string $sent_at) {
        $this->attributes['sent_at'] = $sent_at;
        return $this; }

    public function getIsSeen() {
        return $this->attributes['is_seen']; }
    public function setIsSeen(bool $is_seen) {
        $this->attributes['is_seen'] = $is_seen;
        return $this; }

    public function getIsDeleted() {
        return $this->attributes['is_deleted']; }
    public function setIsDeleted(bool $is_deleted) {
        $this->attributes['is_deleted'] = $is_deleted;
        return $this; }
}
