<?php

namespace App\Repositories;

use App\Models\PostModel;

class PostRepository
{
    protected PostModel $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    public function getPostsByTitle(string $title): array
    {
        if (empty($title))
            return [];

        $rows = $this->model->like('title', $title)
            ->where('is_approved', true)
            ->where('is_deleted', false)
            ->findAll();

        $posts = [];
        foreach ($rows as $row) {
            $levenshteinDistance = levenshtein($title, $row->getTitle());
            $maxLength = max(strlen($title), strlen($row->getTitle()));
            $matchPercentage = $maxLength == 0 ? 100 : (1 - ($levenshteinDistance / $maxLength)) * 100;
            $posts[] = [
                'post' => $row,
                'matchPercentage' => round($matchPercentage, 2)
            ];
        }

        usort($posts, fn($a, $b) => $b['matchPercentage'] - $a['matchPercentage']);

        $sortedPosts = [];
        foreach ($posts as $post) {
            $sortedPosts[] = $post['post']->toArray();
        }

        return $sortedPosts;
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
            log_message('error', '[PostRepository::getBlockedIds] Exceção: ' . $e->getMessage());
            return [];
        }
    }

    public function findAll(?int $viewerId = null): array
    {
        $blockedIds = $this->getBlockedIds($viewerId);
        $builder = $this->model->builder();

        if (!empty($blockedIds)) {
            $builder->whereNotIn('post.id_user', $blockedIds);
        }

        return $builder->get()->getResult();
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function createPost(array $data): int|false
    {
        try {
            return $this->model->insert($data, true);
        } catch (\Throwable $e) {
            log_message('error', '[PostRepository::createPost] ' . $e->getMessage());
            return false;
        }
    }

    public function findAllByCommunity(int $communityId, ?int $viewerId = null): array
    {
        $blockedIds = $this->getBlockedIds($viewerId);
        $builder = $this->model->builder()->where('id_community', $communityId);

        if (!empty($blockedIds)) {
            $builder->whereNotIn('post.id_user', $blockedIds);
        }

        return $builder->get()->getResult();
    }

    public function getPopularPosts(?int $page = 1, ?int $postsPerPage = 30, ?int $viewerId = null)
    {
        $blockedIds = $this->getBlockedIds($viewerId);
        $builder = $this->model->builder();

        $builder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0)
            ->where('post.posted_at >=', date('Y-m-d H:i:s', strtotime('-1 day')));

        if (!empty($blockedIds)) {
            $builder->whereNotIn('post.id_user', $blockedIds);
        }

        $builder->groupBy('post.id')->orderBy('score', 'DESC')->limit($postsPerPage, ($page - 1) * $postsPerPage);

        return $builder->get()->getResultArray();
    }
}