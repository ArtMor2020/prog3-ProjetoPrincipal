<?php

namespace App\Repositories;

use App\Models\UserInCommunityModel;
use App\Entities\UserInCommunityEntity;

class UserInCommunityRepository
{
    protected UserInCommunityModel $model;

    public function __construct()
    {
        $this->model = new UserInCommunityModel();
    }

    public function addMember(int $communityId, int $userId, string $role = 'member'): bool
    {
        if (
            $this->model->where('id_user', $userId)
                ->where('id_community', $communityId)
                ->first()
        ) {
            return false;
        }

        return (bool) $this->model->insert([
            'id_user' => $userId,
            'id_community' => $communityId,
            'role' => $role,
            'is_banned' => false,
        ]);
    }

    public function removeMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model
            ->where('id_user', $userId)
            ->where('id_community', $communityId)
            ->delete();
    }

    public function updateRole(int $communityId, int $userId, string $role): bool
    {
        try {
            return (bool) $this->model
                ->where('id_user', $userId)
                ->where('id_community', $communityId)
                ->set('role', $role)
                ->update();
        } catch (\Throwable $e) {
            log_message('error', '[updateRole] ' . $e->getMessage());
            return false;
        }
    }

    public function banMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->set('is_banned', true)
            ->update();  // sem parâmetros aqui
    }

    public function unbanMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->set('is_banned', false)
            ->update();
    }

    public function listByCommunity(int $communityId): array
    {
        return $this->model
            ->where('id_community', $communityId)
            ->findAll();
    }

    public function listByUser(int $userId): array
    {
        return $this->model
            ->where('id_user', $userId)
            ->findAll();
    }
}