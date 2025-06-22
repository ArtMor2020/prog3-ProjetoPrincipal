<?php

namespace App\Repositories;

use App\Models\UserInCommunityModel;
use App\Entities\UserInCommunityEntity;
use Throwable;

class UserInCommunityRepository
{
    protected UserInCommunityModel $model;

    public function __construct()
    {
        $this->model = new UserInCommunityModel();
    }

    public function addMember(int $communityId, int $userId, string $role = 'member'): bool
    {
        if ($this->getMembership($communityId, $userId)) {
            return false;
        }

        $data = [
            'id_user' => $userId,
            'id_community' => $communityId,
            'role' => $role,
            'is_banned' => false,
        ];

        return $this->model->builder()->insert($data);
    }

    public function removeMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model->builder()
            ->where('id_user', $userId)
            ->where('id_community', $communityId)
            ->delete();
    }

    public function updateRole(int $communityId, int $userId, string $role): bool
    {
        try {
            return (bool) $this->model->builder()
                ->where('id_user', $userId)
                ->where('id_community', $communityId)
                ->set('role', $role)
                ->update();
        } catch (Throwable $e) {
            log_message('error', '[updateRole] ' . $e->getMessage());
            return false;
        }
    }

    public function inviteUserToCommunity(int $communityId, int $userId): bool
    {
        $isAlreadyRelated = (bool) $this->getMembership($communityId, $userId);
        if ($isAlreadyRelated)
            return false;
        return $this->addMember($communityId, $userId, 'invited');
    }

    public function acceptInvite(int $communityId, int $userId)
    {
        return $this->updateRole($communityId, $userId, 'member');
    }

    public function banMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model->builder()
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->set('is_banned', true)
            ->update();
    }

    public function unbanMember(int $communityId, int $userId): bool
    {
        return (bool) $this->model->builder()
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->set('is_banned', false)
            ->update();
    }

    public function listByCommunity(int $communityId): array
    {
        return $this->model->where('id_community', $communityId)->findAll();
    }

    public function listByUser(int $userId): array
    {
        return $this->model->where('id_user', $userId)->findAll();
    }

    public function listAdministratorsByCommunity(int $communityId)
    {
        return $this->model
            ->where('id_community', $communityId)
            ->whereIn('role', ['ADMIN', 'MODERATOR'])
            ->findAll();
    }

    public function getMembership(int $communityId, int $userId): ?UserInCommunityEntity
    {
        return $this->model
            ->where('id_community', $communityId)
            ->where('id_user', $userId)
            ->first();
    }
}