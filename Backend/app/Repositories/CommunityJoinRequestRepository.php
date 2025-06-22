<?php

namespace App\Repositories;

use App\Models\CommunityJoinRequestModel;
use Throwable;

class CommunityJoinRequestRepository
{
    protected CommunityJoinRequestModel $model;

    public function __construct()
    {
        $this->model = new CommunityJoinRequestModel();
    }

    public function create(int $communityId, int $userId): bool|int
    {
        if ($this->getPendingRequest($communityId, $userId)) {
            return false;
        }

        $data = [
            'id_community' => $communityId,
            'id_user' => $userId,
            'requested_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
        ];

        try {
            return $this->model->insert($data, true);
        } catch (Throwable $e) {
            log_message('error', '[CommunityJoinRequestRepository::create] ' . $e->getMessage());
            return false;
        }
    }

    protected function updateStatus(int $requestId, string $status): bool
    {
        return (bool) $this->model
            ->where('id', $requestId)
            ->set('status', $status)
            ->update();
    }

    public function approve(int $requestId): bool
    {
        return $this->updateStatus($requestId, 'approved');
    }

    public function reject(int $requestId): bool
    {
        return $this->updateStatus($requestId, 'rejected');
    }

    public function listAll(): array
    {
        return $this->model->orderBy('requested_at', 'DESC')->findAll();
    }

    public function getRequest(int $requestId)
    {
        return $this->model->find($requestId);
    }

    public function listByCommunity(int $communityId): array
    {
        return $this->model->where('id_community', $communityId)
            ->orderBy('requested_at', 'DESC')
            ->findAll();
    }

    public function listByUser(int $userId): array
    {
        return $this->model->where('id_user', $userId)
            ->orderBy('requested_at', 'DESC')
            ->findAll();
    }

    public function getPendingRequest(int $communityId, int $userId)
    {
        return $this->model
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->where('status', 'pending')
            ->first();
    }
}