<?php

namespace App\Repositories;

use App\Entities\UserEntity;
use App\Models\UserModel;
use Exception;

class UserRepository
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Get user by ID.
     *
     * @param int $id_user
     * @return UserEntity|false
     */
    public function getUserById(int $id_user): UserEntity|false
    {
        if (empty($id_user)) return false;

        try {
            $user = $this->userModel->find($id_user);
            return $user instanceof UserEntity ? $user : false;
        } catch (\Throwable $e) {
            error_log('[getUserById] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new user.
     *
     * @param UserEntity $userEntity
     * @return int|false
     */
    public function createUser(UserEntity $userEntity): int|false
    {
        try {
            $existing = $this->userModel
                ->where('email', $userEntity->getEmail())
                ->first();

            if ($existing) return false;

            return $this->userModel->insert($userEntity, true);
        } catch (\Throwable $e) {
            error_log('[createUser] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing user.
     *
     * @param UserEntity $userEntity
     * @return bool
     */
    public function updateUser(UserEntity $userEntity): bool
    {
        $id = $userEntity->getId();
        if (empty($id)) return false;

        try {
            return $this->userModel->save($userEntity);
        } catch (\Throwable $e) {
            error_log('[updateUser] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ban a user.
     */
    public function banUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_banned', true, '[banUser]');
    }

    /**
     * Unban a user.
     */
    public function unbanUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_banned', false, '[unbanUser]');
    }

    /**
     * Soft delete a user.
     */
    public function deleteUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_deleted', true, '[deleteUser]');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_deleted', false, '[restoreUser]');
    }

    /**
     * Generic helper to update a boolean flag.
     */
    private function setFlag(int $id_user, string $field, bool $value, string $context): bool
    {
        if (empty($id_user)) return false;

        try {
            return $this->userModel->update($id_user, [$field => $value]);
        } catch (\Throwable $e) {
            error_log("$context " . $e->getMessage());
            return false;
        }
    }
}
