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

    /**
     * Block a user.
     *
     * @param BlockedUserEntity $entity
     * @return bool
     */
    public function blockUser(BlockedUserEntity $entity): bool
    {
        $id_user = $entity->getIdUser();
        $id_blocked_user = $entity->getIdBlockedUser();

        if (empty($id_user) || empty($id_blocked_user) || $id_user === $id_blocked_user) {
            return false;
        }

        try {
            if ($this->isUserBlocked($id_user, $id_blocked_user)) {
                return true;
            }

            return $this->blockedUserModel->insert($entity) !== false;

        } catch (Throwable $e) {
            error_log('[blockUser] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unblock a user.
     *
     * @param int $id_user
     * @param int $id_blocked_user
     * @return bool
     */
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

    /**
     * Check if a user is blocked.
     *
     * @param int $id_user
     * @param int $id_blocked_user
     * @return bool
     */
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

    /**
     * Get all users blocked by a specific user.
     *
     * @param int $id_user
     * @return BlockedUserEntity[]|false
     */
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
