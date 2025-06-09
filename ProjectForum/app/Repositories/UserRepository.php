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
            log_message('error','[getUserById] ' . $e->getMessage());
            return false;
        }
    }

    public function getUserByName(string $name): array|null
    {
        if (empty($name)) return null;

        $name = $this->sanitizeName($name);
        $user = $this->userModel->where('name', $name)->first();

        return $user->toArray();
    }

    public function getUsersByName(string $name): array|null
    {
        if (empty($name)) return null;

        $name = $this->sanitizeName($name);
        
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

    public function isNameAvailable(string $name, ?int $ignoreId = null): bool
    {
        if (empty($name)) return false;

        $name = $this->sanitizeName($name);
        
        $query = $this->userModel->where('name', $name);
        
        if ($ignoreId !== null) {
            $query->where('id !=', $ignoreId);
        }

        return $query->first() === null;
    }

    public function createUser(UserEntity $userEntity): int|false
    {
        if ($this->userModel->where('email', $userEntity->getEmail())->first()) {
            return false;
        }

        if(!$this->isNameAvailable($this->sanitizeName($userEntity->getName()))) return false;

        $data = [
            'name' => $this->sanitizeName($userEntity->getName()),
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
        if(empty($id) || empty($data)) return false;

        unset($data['password_confirm']);
        unset($data['email']);

        $data['name'] = $this->sanitizeName($data['name']);

        if(!$this->isNameAvailable($data['name'], $id)) return false;

        try {
            return (bool) $this->userModel->update($id, $data);
        } catch (\Throwable $e) {
            log_message('error','[updateUser] ' . $e->getMessage());
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
            log_message('error',"$context " . $e->getMessage());
            return false;
        }
    }

    private function sanitizeName(string $name): string  // removes all whitespaces
    {
        return trim(preg_replace('/\s+/', '', $name));  
    }

}
