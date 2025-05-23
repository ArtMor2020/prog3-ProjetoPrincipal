<?php

namespace App\Repositories;

use App\Models\PostViewModel;
use App\Entities\PostViewEntity;
use CodeIgniter\Database\ConnectionInterface;
use Throwable;

class PostViewRepository
{
    protected PostViewModel $model;
    protected ConnectionInterface $db;
    protected \CodeIgniter\Database\BaseBuilder $builder;

    public function __construct()
    {
        $this->model = new PostViewModel();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table($this->model->table);
    }

    public function addView(int $postId, int $userId): bool
    {
        try {
            $now = date('Y-m-d H:i:s');

            // já existe?
            $exists = $this->builder
                ->where('id_post', $postId)
                ->where('id_user', $userId)
                ->countAllResults(false);

            if ($exists > 0) {
                return (bool) $this->builder
                    ->where('id_post', $postId)
                    ->where('id_user', $userId)
                    ->update(['viewed_at' => $now]);
            }

            return (bool) $this->builder->insert([
                'id_post' => $postId,
                'id_user' => $userId,
                'viewed_at' => $now,
            ]);
        } catch (Throwable $e) {
            log_message('error', '[PostViewRepository::addView] ' . $e->getMessage());
            return false;
        }
    }

    public function getViewsCount(int $postId): int
    {
        return (int) $this->builder
            ->where('id_post', $postId)
            ->countAllResults();
    }

    public function listViewers(int $postId): array
    {
        return $this->builder
            ->where('id_post', $postId)
            ->orderBy('viewed_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}