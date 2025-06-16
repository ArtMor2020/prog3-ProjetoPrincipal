<?php

namespace App\Repositories;

use App\Models\CommunityViewModel;
use CodeIgniter\Database\ConnectionInterface;

class CommunityViewRepository
{
    protected CommunityViewModel $model;
    protected ConnectionInterface $db;

    public function __construct()
    {
        $this->model = new CommunityViewModel();
        $this->db = \Config\Database::connect();
    }

    public function addView(int $communityId, int $userId): bool
    {
        $timestamp = date('Y-m-d H:i:s');
        $builder = $this->db->table($this->model->table);

        $existing = $builder
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->get()
            ->getFirstRow();

        if ($existing) {
            return (bool) $builder
                ->where('id_community', $communityId)
                ->where('id_user', $userId)
                ->set('viewed_at', $timestamp)
                ->update();
        }

        return (bool) $builder->insert([
            'id_community' => $communityId,
            'id_user' => $userId,
            'viewed_at' => $timestamp,
        ]);
    }

    public function listByCommunity(int $communityId): array
    {
        return $this->model
            ->where('id_community', $communityId)
            ->orderBy('viewed_at', 'DESC')
            ->findAll();
    }
    public function listByUser(int $userId): array
    {
        return $this->model
            ->where('id_user', $userId)
            ->orderBy('viewed_at', 'DESC')
            ->findAll();
    }
}
