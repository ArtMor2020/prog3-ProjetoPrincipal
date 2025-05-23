<?php

namespace App\Repositories;

use App\Models\CommunityModel;
use App\Entities\CommunityEntity;

class CommunityRepository
{
    protected CommunityModel $model;

    public function __construct()
    {
        $this->model = new CommunityModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function findById(int $id): ?CommunityEntity
    {
        return $this->model->find($id);
    }

    public function createCommunity(array $data): int|false
    {

        if ($this->model->where('name', $data['name'])->first()) {
            return false;
        }

        return $this->model->insert($data, true);
    }

    public function updateCommunity(int $id, array $data): bool
    {
        return (bool) $this->model->update($id, $data);
    }

    public function deleteCommunity(int $id): bool
    {
        $community = $this->model->find($id);
        if (!$community || $community->getIsDeleted()) {
            return false;
        }
        return (bool) $this->model->update($id, ['is_deleted' => true]);
    }

    public function banCommunity(int $id): bool
    {
        return $this->setFlag($id, 'is_banned', true, '[banCommunity]');
    }

    public function unbanCommunity(int $id): bool
    {
        return $this->setFlag($id, 'is_banned', false, '[unbanCommunity]');
    }

    public function restoreCommunity(int $id): bool
    {
        return $this->setFlag($id, 'is_deleted', false, '[restoreCommunity]');
    }

    private function setFlag(int $id, string $field, bool $value, string $context): bool
    {
        if (empty($id)) {
            return false;
        }
        try {
            return (bool) $this->model->update($id, [$field => $value]);
        } catch (\Throwable $e) {
            error_log("$context " . $e->getMessage());
            return false;
        }
    }

    public function findByOwner(int $ownerId): array
    {
        return $this->model->where('id_owner', $ownerId)->findAll();
    }

    public function searchByName(string $name): array
    {
        return $this->model->like('name', $name)->findAll();
    }
}
