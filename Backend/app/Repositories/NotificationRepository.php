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

    public function notifyUser(int $userId, string $type, $originId): bool|int
    {
        $notification = new NotificationEntity();
        $notification->id_user = $userId;
        $notification->status = 'not_seen';
        $notification->event_date = date('Y-m-d H:i:s');
        $notification->type = $type;
        $notification->id_origin = $originId;

        try {
            if ($this->notificationModel->save($notification)) {
                return $this->notificationModel->getInsertID();
            }
            return false;
        } catch (Throwable $e) {
            log_message('error', '[NotificationRepository::notifyUser] Exceção ao salvar: ' . $e->getMessage());
            return false;
        }
    }

    public function existsUnreadNotification(int $userId, $originId, string $type): bool
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