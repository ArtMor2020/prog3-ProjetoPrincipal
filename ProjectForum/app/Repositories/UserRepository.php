<?php

namespace App\Repositories;

use App\Models\UserModel;
use App\Entities\UserEntity;
use Exception;

class UserRepository
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function findAll(): array
    {
        return $this->userModel->findAll();
    }


    public function getUserById(int $id_user): UserEntity|false
    {
        if (empty($id_user))
            return false;

        try {
            $user = $this->userModel->find($id_user);
            return $user instanceof UserEntity ? $user : false;
        } catch (\Throwable $e) {
            error_log('[getUserById] ' . $e->getMessage());
            return false;
        }
    }

    public function getUsersByName(string $name): array{

        // gets all users with 'name' similar to $name
        $rows = $this->userModel->like('name', $name)
                            ->where('is_private', false)
                            ->where('is_banned', false)
                            ->where('is_deleted', false)
                            ->findAll();
        
        $users = [];

        // finds how similar the 'name' and $name are using levenshtein distance
        foreach($rows as $row){

            $levenshteinDistance = levenshtein($name, $row->getName());
            $maxLength = max(strlen($name), strlen($row->getName()));

            $matchPercentage = $maxLength == 0 ? 100 : (1 - ($levenshteinDistance / $maxLength)) * 100;

            $users[] = [
                'user' => $row,
                'matchPercentage' => round($matchPercentage, 2)
            ];
        }

        // sorts by similarity
        usort($users, function($a, $b) {
            return $b['matchPercentage'] - $a['matchPercentage']; 
        });

        $sortedUsers = [];

        // extracts user model from array
        foreach($users as $user){
            $sortedUsers[] = $user['user']->toArray();
        }

        return $sortedUsers;
    }

    public function createUser(UserEntity $userEntity): int|false
    {
        if ($this->userModel->where('email', $userEntity->getEmail())->first()) {
            return false;
        }

        $data = [
            'name' => $userEntity->getName(),
            'email' => $userEntity->getEmail(),
            'password' => $userEntity->getPassword(),
            'about' => $userEntity->getAbout(),
            'is_private' => $userEntity->getIsPrivate(),
            'is_banned' => $userEntity->getIsBanned(),
            'is_deleted' => $userEntity->getIsDeleted(),
        ];

        return $this->userModel->insert($data, true);
    }

    public function updateUser(int $id, array $data): bool
    {
        unset($data['password_confirm']);
        unset($data['email']);

        try {
            return (bool) $this->userModel->update($id, $data);
        } catch (\Throwable $e) {
            error_log('[updateUser] ' . $e->getMessage());
            return false;
        }
    }

    public function banUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_banned', true, '[banUser]');
    }

    public function unbanUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_banned', false, '[unbanUser]');
    }

    public function deleteUser(int $id_user): bool
    {
        $user = $this->userModel->find($id_user);

        if (!$user || $user->getIsDeleted()) {
            return false;
        }

        return (bool) $this->userModel->update($id_user, ['is_deleted' => true]);
    }

    public function restoreUser(int $id_user): bool
    {
        return $this->setFlag($id_user, 'is_deleted', false, '[restoreUser]');
    }

    private function setFlag(int $id_user, string $field, bool $value, string $context): bool
    {
        if (empty($id_user))
            return false;

        try {
            return $this->userModel->update($id_user, [$field => $value]);
        } catch (\Throwable $e) {
            error_log("$context " . $e->getMessage());
            return false;
        }
    }
}
