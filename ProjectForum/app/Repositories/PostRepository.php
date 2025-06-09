<?php

namespace App\Repositories;

use App\Models\PostModel;
use App\Entities\PostEntity;
use DateTime;

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

    public function getPostsByTitle(string $title): array{
        
        // gets all 'title's similar to $title
        $rows = $this->model->like('title', $title)
                            ->where('is_approved', true)
                            ->where('is_deleted', false)
                            ->findAll();
        
        $posts = [];

        // finds how similar the 'title' and $title are using levenshtein distance
        foreach($rows as $row){

            $levenshteinDistance = levenshtein($title, $row->getTitle());
            $maxLength = max(strlen($title), strlen($row->getTitle()));

            $matchPercentage = $maxLength == 0 ? 100 : (1 - ($levenshteinDistance / $maxLength)) * 100;

            $posts[] = [
                'post' => $row,
                'matchPercentage' => round($matchPercentage, 2)
            ];
        }

        // sorts by similariry
        usort($posts, function($a, $b) {
            return $b['matchPercentage'] - $a['matchPercentage']; 
        });

        $sortedPosts = [];

        // extracts post model from array
        foreach($posts as $post){
            $sortedPosts[] = $post['post']->toArray();
        }

        return $sortedPosts;
    }

    public function getPostsFromCommunityByPopularityInPeriod(int $communityId, ?string $period = 'day', ?int $page = 1, ?int $postsPerPage = 30): array
    {
        $page = max(1, $page);
        $postsPerPage = max(1, $postsPerPage);

        $offset = ($page - 1) * $postsPerPage;

        // $period must be one from bellow
        // gets the minimum date for the query based on $period
        $dateFrom = match ($period) {
            'day'   => date('Y-m-d H:i:s', strtotime('-1 day')),
            'week'  => date('Y-m-d H:i:s', strtotime('-1 week')),
            'month' => date('Y-m-d H:i:s', strtotime('-1 month')),
            'year'  => date('Y-m-d H:i:s', strtotime('-1 year')),
            default => null
        };

        $builder = $this->model->builder();

        // uses 'post' and 'rating_in_post'
        // counts upvotes in 'rating_in_post'
        // removes not approved and deleted posts
        // removes posts older than dictaded by $period
        // then sorts 'posts' using the upvotes counted
        $builder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.id_community', $communityId)
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0);

        if($dateFrom !== null){
            $builder->where('post.posted_at >=', $dateFrom);
        }

        $builder->groupBy('post.id')
            ->orderBy('score', 'DESC')
            ->limit($postsPerPage, $offset);

        return $builder->get()->getResultArray();
    }

    public function getRecommendedPostsForUser(int $userId, ?int $page = 1, ?int $postsPerPage = 30): array
    {
        $page = max(1, $page);
        $postsPerPage = max(1, $postsPerPage);

        $offset = ($page - 1) * $postsPerPage;        

        $db = \Config\Database::connect();

        // Get community IDs where user is a member and not banned
        $communityIds = $db->table('user_in_community')
            ->select('id_community')
            ->where('id_user', $userId)
            ->where('is_banned', false)
            ->get()
            ->getResultArray();

        // Extract flat array of community IDs
        $communityIds = array_column($communityIds, 'id_community');

        if (empty($communityIds)) {
            return []; // no communities = no posts
        }

        // Build the query
        $postBuilder = $this->model->builder();

        $postBuilder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0)
            ->where('post.posted_at >=', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->whereIn('post.id_community', $communityIds)
            ->groupBy('post.id')
            ->orderBy('score', 'DESC')
            ->limit($postsPerPage, $offset);

        return $postBuilder->get()->getResultArray();
    }


    public function getPopularPosts(?int $page = 1, ?int $postsPerPage = 30){  // gets posts popular today across all communities

        $page = max(1, $page);
        $postsPerPage = max(1, $postsPerPage);

        $offset = ($page - 1) * $postsPerPage;

        $builder = $this->model->builder();

        log_message('error','1');

        $builder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0)
            ->where('post.posted_at >=', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->groupBy('post.id')
            ->orderBy('score', 'DESC')
            ->limit($postsPerPage, $offset);
        
        $posts = $builder->get()->getResultArray();

        log_message('error','2');
        
        return $posts;
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
            log_message('error','[PostRepository::createPost] ' . $e->getMessage());
            return false;
        }
    }

    public function getPendingPostsForCommunity(int $idCommunity, ?int $page = 1, ?int $postsPerPage = 30)
    {
        $page = max(1, $page);
        $postsPerPage = max(1, $postsPerPage);

        $offset = ($page - 1) * $postsPerPage;

        return $this->model->where('id_community', $idCommunity)
                            ->where('is_approved', 0)
                            ->orderBy('posted_at', 'ASC')
                            ->findAll($postsPerPage, $offset);
    }

    public function approvePost(int $idPost): bool
    {
        return (bool) $this->model->update($idPost, ['is_approved', true]);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->model->update($id, $data);
    }

    public function deletePost(int $id): bool
    {
        $post = $this->model->find($id);

        if (!$post || $post->getIsDeleted()) {
            return false;
        }

        return (bool) $this->model->update($id, ['is_deleted' => true]);
    }

    public function restorePost(int $id): bool
    {
        $post = $this->model->find($id);

        if (!$post || !$post->getIsDeleted()) {
            return false;
        }

        return (bool) $this->model->update($id, ['is_deleted' => false]);       
    }
}
