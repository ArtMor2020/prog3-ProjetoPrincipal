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

    private function getBlockedIds(?int $viewerId): array
    {
        if (!$viewerId) {
            return [];
        }
        try {
            $blockedUsers = db_connect()->table('blocked_user')
                ->select('id_blocked_user')
                ->where('id_user', $viewerId)
                ->get()
                ->getResultArray();
            return array_column($blockedUsers, 'id_blocked_user');
        } catch (\Throwable $e) {
            log_message('error', '[CommentRepository::getBlockedIds] Exceção: ' . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?CommentEntity
    {
        return $this->model->find($id);
    }

    public function findAllByPost(int $postId, ?int $viewerId = null): array
    {
        $blockedIds = $this->getBlockedIds($viewerId);
        $builder = $this->model->builder()->where('id_parent_post', $postId);

        if (!empty($blockedIds)) {
            $builder->whereNotIn('comment.id_user', $blockedIds);
        }

        return $builder->get()->getResult();
    }

    public function findByParentComment(int $parentCommentId): array
    {
        return $this->model->builder()->where('id_parent_comment', $parentCommentId)->get()->getResult();
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->model->insert($data);
    }

    public function createReply(int $parentCommentId, array $data): int|false
    {
        $parent = $this->model->find($parentCommentId);
        if (!$parent)
            return false;

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