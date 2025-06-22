<?php

namespace App\Repositories;

use App\Models\AttachmentInPostModel;
use Throwable;

class AttachmentInPostRepository
{
    private AttachmentInPostModel $model;

    public function __construct()
    {
        $this->model = new AttachmentInPostModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function find(int $postId, int $attachmentId): ?array
    {
        return $this->model
            ->where('id_post', $postId)
            ->where('id_attachment', $attachmentId)
            ->first();
    }

    public function findAttachmentsInPost(int $postId): array
    {
        return $this->model->where('id_post', $postId)->findAll();
    }

    public function create(int $postId, int $attachmentId): bool
    {
        try {
            return (bool) $this->model->builder()->insert([
                'id_post' => $postId,
                'id_attachment' => $attachmentId,
            ]);
        } catch (Throwable $e) {
            log_message('error', '[AttachmentInPostRepository::create] ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $postId, int $attachmentId): bool
    {
        return (bool) $this->model->builder()
            ->where('id_post', $postId)
            ->where('id_attachment', $attachmentId)
            ->delete();
    }
}