<?php

namespace App\Repositories;

use App\Entities\BlockedUserEntity;
use App\Models\BlockedUserModel;
use Throwable;

class BlockedUserRepository
{
    protected BlockedUserModel $blockedUserModel;

    public function __construct()
    {
        $this->blockedUserModel = new BlockedUserModel();
    }

    public function blockUser(BlockedUserEntity $entity): bool
    {
        $id_user = $entity->getIdUser();
        $id_blocked_user = $entity->getIdBlockedUser();

        if (empty($id_user) || empty($id_blocked_user) || $id_user === $id_blocked_user) {
            return false;
        }

        if ($this->isUserBlocked($id_user, $id_blocked_user)) {
            return true;
        }

        $data = [
            'id_user' => $id_user,
            'id_blocked_user' => $id_blocked_user,
        ];

        return (bool) $this->blockedUserModel
            ->db
            ->table('blocked_user')
            ->insert($data);
    }

    public function unblockUser(int $id_user, int $id_blocked_user): bool
    {
        if (empty($id_user) || empty($id_blocked_user) || $id_user === $id_blocked_user) {
            return false;
        }

        try {
            return $this->blockedUserModel
                ->where('id_user', $id_user)
                ->where('id_blocked_user', $id_blocked_user)
                ->delete();

        } catch (Throwable $e) {
            error_log('[unblockUser] ' . $e->getMessage());
            return false;
        }
    }

    public function isUserBlocked(int $id_user, int $id_blocked_user): bool
    {
        if (empty($id_user) || empty($id_blocked_user) || $id_user === $id_blocked_user) {
            return false;
        }

        try {
            return $this->blockedUserModel
                ->where('id_user', $id_user)
                ->where('id_blocked_user', $id_blocked_user)
                ->first() !== null;

        } catch (Throwable $e) {
            error_log('[isUserBlocked] ' . $e->getMessage());
            return false;
        }
    }

    public function getBlockedUsers(int $id_user): array|false
    {
        if (empty($id_user)) {
            return false;
        }

        try {
            return $this->blockedUserModel
                ->where('id_user', $id_user)
                ->findAll();

        } catch (Throwable $e) {
            error_log('[getBlockedUsers] ' . $e->getMessage());
            return false;
        }
    }
}
