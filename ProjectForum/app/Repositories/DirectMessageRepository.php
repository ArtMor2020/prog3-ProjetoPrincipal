<?php

namespace App\Repositories;

use App\Models\DirectMessageModel;
use App\Entities\DirectMessageEntity;

class DirectMessageRepository
{
    protected DirectMessageModel $model;

    public function __construct()
    {
        $this->model = new DirectMessageModel();
    }

    public function sendMessage(array $data): int
    {
        return $this->model->insert($data);
    }

    public function getConversation(int $userA, int $userB): array
    {
        return $this->model->whereIn('id_sender', [$userA, $userB])
            ->whereIn('id_reciever', [$userA, $userB])
            ->orderBy('sent_at', 'ASC')
            ->findAll();
    }

    public function markAsSeen(int $id): bool
    {
        return $this->model->update($id, ['is_seen' => true]);
    }
}