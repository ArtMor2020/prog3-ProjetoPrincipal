<?php

namespace App\Repositories;

use App\Models\AttachmentModel;
use App\Entities\AttachmentEntity;
use Throwable;

class AttachmentRepository
{
    protected AttachmentModel $model;

    public function __construct()
    {
        $this->model = new AttachmentModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function findById(int $id): ?AttachmentEntity
    {
        return $this->model->find($id);
    }

    public function create(array $data): int|false
    {
        try {
            return $this->model->insert($data, true);
        } catch (Throwable $e) {
            log_message('error', "[AttachmentRepository::create] {$e->getMessage()}");
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            return (bool) $this->model->update($id, $data);
        } catch (Throwable $e) {
            log_message('error', "[AttachmentRepository::update] {$e->getMessage()}");
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            return (bool) $this->model->update($id, ['is_deleted' => true]);
        } catch (Throwable $e) {
            log_message('error', "[AttachmentRepository::delete] {$e->getMessage()}");
            return false;
        }
    }

    public function restore(int $id): bool
    {
        try {
            return (bool) $this->model->update($id, ['is_deleted' => false]);
        } catch (Throwable $e) {
            log_message('error', "[AttachmentRepository::restore] {$e->getMessage()}");
            return false;
        }
    }
}
