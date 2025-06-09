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
                // so user can make another request after a week,
                ->where('requested_at >=', date('Y-m-d H:i:s', strtotime('-7 day')))
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

    protected function updateStatus(int $id, string $status): bool
    {
        return (bool) $this->model
            ->builder()
            ->where('id', $id)
            ->update(['status' => $status]);
    }

    public function approve(int $idRequest): bool
    {
        return $this->updateStatus($idRequest, 'approved');
    }

    public function reject(int $idRequest): bool
    {
        return $this->updateStatus($idRequest, 'rejected');
    }

    public function listAll(): array
    {
        return $this->model->orderBy('requested_at', 'DESC')->findAll();
    }

    public function getRequest(int $idRequest): array|null
    {
        return $this->model->where('id', $idRequest)
                            ->first();
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
