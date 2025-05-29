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

    public function getPostsFromCommunityByPopularityInPeriod(int $communityId, string $period): array
    {
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
            ->orderBy('score', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getRecommendedPostsForUser(int $userId): array
    {
        // get communities  where the user is a member and not banned
        $db = \Config\Database::connect();
        $builder = $db->table('user_in_community');

        $subquery = $builder
            ->select('id_community')
            ->where('id_user', $userId)
            ->where('is_banned', false)
            ->getCompiledSelect();

        // get posts from those communities with popularity score
        $postBuilder = $this->model->builder();

        $postBuilder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0)
            ->where('post.posted_at >=', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->whereIn('post.id_community', $db->query($subquery)->getResultArray())
            ->groupBy('post.id')
            ->orderBy('score', 'DESC');

        $posts = $postBuilder->get()->getResultArray();

        return $posts;
    }

    public function getPopularPosts(){  // gets posts popular today across all communities

        $builder = $this->model->builder();

        $builder->select('post.*, COALESCE(SUM(CASE WHEN rating_in_post.is_upvote = 1 THEN 1 ELSE -1 END), 0) AS score')
            ->join('rating_in_post', 'post.id = rating_in_post.id_post', 'left')
            ->where('post.is_approved', 1)
            ->where('post.is_deleted', 0)
            ->where('post.posted_at >=', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->groupBy('post.id')
            ->orderBy('score', 'DESC');
        
        $posts = $builder->get()->getResultArray();

        return  $posts;
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
