<?php

namespace App\Repositories;

use App\Models\RatingInPostModel;
use App\Entities\RatingInPostEntity;

class RatingInPostRepository
{
    protected RatingInPostModel $model;

    public function __construct()
    {
        $this->model = new RatingInPostModel();
    }

    public function toggleVote(int $postId, int $userId, bool $isUpvote): bool
    {
        $builder = $this->model->builder();

        $existing = $builder
            ->where('id_post', $postId)
            ->where('id_user', $userId)
            ->get()
            ->getRow();

        if ($existing) {

            if ((bool) $existing->is_upvote === $isUpvote) {
                return (bool) $builder
                    ->where('id_post', $postId)
                    ->where('id_user', $userId)
                    ->delete();
            }

            return (bool) $builder
                ->set('is_upvote', $isUpvote)
                ->where('id_post', $postId)
                ->where('id_user', $userId)
                ->update();
        }

        $builder->set('id_post', $postId)
            ->set('id_user', $userId)
            ->set('is_upvote', $isUpvote);
        return (bool) $builder->insert();
    }

    public function getVotesForPost(int $postId): array
    {
        return $this->model
            ->where('id_post', $postId)
            ->findAll();
    }

    public function getScore(int $postId): int
    {
        $builderUp = $this->model->builder();
        $up = $builderUp
            ->where('id_post', $postId)
            ->where('is_upvote', true)
            ->countAllResults();

        $builderDown = $this->model->builder();
        $down = $builderDown
            ->where('id_post', $postId)
            ->where('is_upvote', false)
            ->countAllResults();

        return $up - $down;
    }
}