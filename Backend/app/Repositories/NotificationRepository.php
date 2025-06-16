<?php

namespace App\Repositories;

use App\Entities\NotificationEntity;
use App\Models\NotificationModel;

class NotificationRepository
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function findAll(): array
    {
        return $this->notificationModel->findAll();
    }

    public function findNotificationsForUser(int $userId)
    {
        return $this->notificationModel->where('id_user', $userId)
                                        ->where('status', 'not_seen')
                                        ->orderBy('event_date', 'DESC')
                                        ->findAll();
    }

    public function notifyUser(int $userId, string $type, int $originId): bool|int
    {
        $notification = new NotificationEntity();

        $notification->setIdUser($userId);
        $notification->setStatus('not_seen');
        $notification->setEventDate(date('Y-m-d H:i:s'));
        $notification->setType($type);
        $notification->setIdOrigin($originId);

        return $this->notificationModel->insert($notification, true);
    }

    public function existsUnreadNotification(int $userId, int $originId, string $type): bool
    {
        return $this->notificationModel->where([
                'id_user'   => $userId,
                'id_origin' => $originId,
                'type'      => $type,
                'status'    => 'not_seen'
            ])
            ->countAllResults() > 0;
    }

    public function clearNotification(int $id): bool
    {
        return (bool) $this->notificationModel->update($id, ['status' => 'seen']);
    }

    public function clearAllNotifications(int $userId): bool
    {
        return (bool) $this->notificationModel->where('id_user', $userId)
            ->where('status', 'not_seen')
            ->set('status', 'seen')
            ->update();
    }

    public function delete($id)
    {
        return (bool) $this->notificationModel->delete($id);
    }
}