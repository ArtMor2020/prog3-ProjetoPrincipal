<?php

namespace App\Repositories;

use App\Models\UserModel;
use App\Entities\UserEntity;
use Throwable;

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

    public function getUserById(int $id_user): ?UserEntity
    {
        if (empty($id_user))
            return null;
        try {
            return $this->userModel->find($id_user);
        } catch (Throwable $e) {
            log_message('error', '[getUserById] ' . $e->getMessage());
            return null;
        }
    }

    public function authenticate(string $email, string $password): ?UserEntity
    {
        $user = $this->userModel->where('email', $email)->where('is_deleted', false)->first();
        if (!$user instanceof UserEntity || !password_verify($password, $user->getPassword())) {
            return null;
        }
        $user->setPassword('');
        return $user;
    }

    public function getUserByName(string $name): ?UserEntity
    {
        if (empty($name))
            return null;
        $name = $this->sanitizeName($name);
        return $this->userModel->where('name', $name)->first();
    }

    public function getUsersByName(string $name): array
    {
        if (empty($name))
            return [];
        $name = $this->sanitizeName($name);
        $rows = $this->userModel->like('name', $name)
            ->where('is_private', false)
            ->where('is_banned', false)
            ->where('is_deleted', false)
            ->findAll();
        $users = [];
        foreach ($rows as $row) {
            $levenshteinDistance = levenshtein($name, $row->getName());
            $maxLength = max(strlen($name), strlen($row->getName()));
            $matchPercentage = $maxLength == 0 ? 100 : (1 - ($levenshteinDistance / $maxLength)) * 100;
            $users[] = [
                'user' => $row,
                'matchPercentage' => round($matchPercentage, 2)
            ];
        }
        usort($users, fn($a, $b) => $b['matchPercentage'] - $a['matchPercentage']);
        return array_map(fn($item) => $item['user']->toArray(), $users);
    }

    public function isNameAvailable(string $name, ?int $ignoreId = null): bool
    {
        if (empty($name))
            return false;
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
        if (!$this->isNameAvailable($this->sanitizeName($userEntity->getName())))
            return false;
        return $this->userModel->insert($userEntity);
    }

    public function updateUser(int $id, array $data): bool
    {
        if (empty($id) || empty($data))
            return false;
        unset($data['password_confirm'], $data['email']);
        $data['name'] = $this->sanitizeName($data['name']);
        if (!$this->isNameAvailable($data['name'], $id))
            return false;
        try {
            return (bool) $this->userModel->update($id, $data);
        } catch (Throwable $e) {
            log_message('error', '[updateUser] ' . $e->getMessage());
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
        return $this->setFlag($id_user, 'is_deleted', true, '[deleteUser]');
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
        } catch (Throwable $e) {
            log_message('error', "$context " . $e->getMessage());
            return false;
        }
    }

    private function sanitizeName(string $name): string
    {
        return trim(preg_replace('/\s+/', '', $name));
    }
}