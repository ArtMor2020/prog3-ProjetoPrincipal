<?php

namespace App\Repositories;

use App\Models\CommentModel;
use App\Entities\CommentEntity;

class CommentRepository
{
    protected CommentModel $model;

    public function __construct()
    {
        $this->model = new CommentModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function findAllByPost(int $postId): array
    {
        return $this->model->where('id_parent_post', $postId)->findAll();
    }

    public function findByParentComment(int $parentCommentId): array
    {
        return $this->model
            ->where('id_parent_comment', $parentCommentId)
            ->findAll();
    }

    public function findById(int $id): ?CommentEntity
    {
        return $this->model->find($id);
    }

    public function create(array $data): int
    {
        $insertData = [
            'id_user' => $data['id_user'],
            'id_parent_post' => $data['id_parent_post'] ?? null,
            'id_parent_comment' => $data['id_parent_comment'] ?? null,
            'content' => $data['content'],
            'is_deleted' => $data['is_deleted'] ?? false,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->model->insert($insertData);
    }

    public function createReply(int $parentCommentId, array $data): int|false
    {
        $parent = $this->model->find($parentCommentId);
        if (!$parent) {
            return false;
        }

        $insert = [
            'id_user' => (int) $data['id_user'],
            'id_parent_post' => $parent->id_parent_post,
            'id_parent_comment' => $parentCommentId,
            'content' => $data['content'],
        ];

        return $this->model->insert($insert, true);
    }


    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function deleteComment(int $id): bool
    {
        $comment = $this->model->find($id);

        if (!$comment || $comment->getIsDeleted()) {
            return false;
        }

        return (bool) $this->model->update($id, ['is_deleted' => true]);
    }
}
