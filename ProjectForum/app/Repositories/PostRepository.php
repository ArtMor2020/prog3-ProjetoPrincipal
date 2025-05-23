<?php

namespace App\Repositories;

use App\Models\PostModel;
use App\Entities\PostEntity;

class PostRepository
{
    protected PostModel $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function findAllByCommunity(int $communityId): array
    {
        return $this->model
            ->where('id_community', $communityId)
            ->findAll();
    }

    public function findById(int $id): ?PostEntity
    {
        return $this->model->find($id);
    }

    public function createPost(array $data): int|false
    {
        try {
            return $this->model->insert($data, true);
        } catch (\Throwable $e) {
            error_log('[PostRepository::createPost] ' . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function deletePost(int $id): bool
    {
        $post = $this->model->find($id);

        if (!$post || $post->getIsDeleted()) {
            return false;
        }

        return (bool) $this->model->update($id, ['is_deleted' => true]);
    }
}
