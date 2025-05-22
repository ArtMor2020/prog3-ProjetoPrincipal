<?php

namespace App\Repositories;

use App\Models\CommunityJoinRequestModel;
use App\Entities\CommunityJoinRequestEntity;

class CommunityJoinRequestRepository
{
    protected CommunityJoinRequestModel $model;

    public function __construct()
    {
        $this->model = new CommunityJoinRequestModel();
    }

    public function create(int $communityId, int $userId): bool
    {
        if (
            $this->model->where('id_community', $communityId)
                ->where('id_user', $userId)
                ->first()
        ) {
            return false;
        }

        $data = [
            'id_community' => $communityId,
            'id_user' => $userId,
            'requested_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
        ];

        return (bool) $this->model
            ->builder()
            ->insert($data);
    }

    protected function updateStatus(int $communityId, int $userId, string $status): bool
    {
        return (bool) $this->model
            ->builder()
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->update(['status' => $status]);
    }

    public function approve(int $communityId, int $userId): bool
    {
        return $this->updateStatus($communityId, $userId, 'approved');
    }

    public function reject(int $communityId, int $userId): bool
    {
        return $this->updateStatus($communityId, $userId, 'rejected');
    }

    public function listAll(): array
    {
        return $this->model->orderBy('requested_at', 'DESC')->findAll();
    }

    public function listByCommunity(int $communityId): array
    {
        return $this->model
            ->where('id_community', $communityId)
            ->orderBy('requested_at', 'DESC')
            ->findAll();
    }

    public function listByUser(int $userId): array
    {
        return $this->model
            ->where('id_user', $userId)
            ->orderBy('requested_at', 'DESC')
            ->findAll();
    }
}
