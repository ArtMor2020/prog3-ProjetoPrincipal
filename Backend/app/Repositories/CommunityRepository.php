<?php

namespace App\Repositories;

use App\Models\CommunityModel;
use App\Entities\CommunityEntity;
use Throwable;

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
        $normalizedNewName = strtolower(trim($data['name']));

        $existingCommunity = $this->model->where('name', $data['name'])->first();

        if ($existingCommunity) {
            $errorMessage = sprintf(
                "Conflito de nome de comunidade. Tentativa de criar: '%s'. Nome já existente no BD: '%s' (ID: %d).",
                $data['name'],
                $existingCommunity->getName(),
                $existingCommunity->getId()
            );
            log_message('error', $errorMessage);

            return false;
        }

        try {
            return $this->model->insert($data, true);
        } catch (Throwable $e) {
            log_message('error', '[CommunityRepository::createCommunity] Exceção na inserção: ' . $e->getMessage());
            return false;
        }
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
            log_message('error', "$context " . $e->getMessage());
            return false;
        }
    }

    public function findByOwner(int $ownerId): array
    {
        return $this->model->where('id_owner', $ownerId)->findAll();
    }

    public function searchByName(string $name): array
    {
        // gets all users with 'name' similar to $name
        $rows = $this->model->like('name', $name)
            ->where('is_banned', false)
            ->where('is_deleted', false)
            ->findAll();
        $communities = [];

        foreach ($rows as $row) {

            $levenshteinDistance = levenshtein($name, $row->getName());
            $maxLength = max(strlen($name), strlen($row->getName()));

            $matchPercentage = $maxLength == 0 ? 100 : (1 - ($levenshteinDistance / $maxLength)) * 100;

            $communities[] = [
                'community' => $row,
                'matchPercentage' => round($matchPercentage, 2)
            ];
        }

        usort($communities, function ($a, $b) {
            return $b['matchPercentage'] - $a['matchPercentage'];
        });

        $sortedCommunities = [];

        foreach ($communities as $community) {
            $sortedCommunities[] = $community['community']->toArray();
        }

        return $sortedCommunities;
    }
}
