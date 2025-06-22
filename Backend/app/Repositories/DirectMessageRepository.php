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

    public function getMessage(int $id): DirectMessageEntity|null
    {
        return $this->model->find($id);
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

    public function getUnseenMessagesForUser(int $userId)
    {
        return $this->model->where('id_reciever', $userId)
            ->where('is_seen', false)
            ->findAll();
    }

    public function getUnreadSummary(int $userId): array
    {
        return $this->model
            ->select('id_sender, COUNT(id) as unread_count')
            ->where('id_reciever', $userId)
            ->where('is_seen', false)
            ->groupBy('id_sender')
            ->findAll();
    }

    public function markConversationAsSeen(int $readerId, int $senderId): bool
    {
        return $this->model
            ->where('id_reciever', $readerId)
            ->where('id_sender', $senderId)
            ->where('is_seen', false)
            ->set('is_seen', true)
            ->update();
    }
}