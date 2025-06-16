<?php

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;

class NotificationController extends ResourceController
{
    protected NotificationRepository $notificationRepository;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository();
        $this->notificationService = new NotificationService();
    }

    public function index($userId = null) // just for testing
    {
        $notifications = $userId
            ? $this->notificationRepository->findNotificationsForUser($userId)
            : $this->notificationRepository->findAll();

        return $this->respond($notifications);
    }

    public function formattedNotifications(int $userId) // returns formated notifications
    {
        $notifications = $this->notificationService->getNotifications($userId);

        return $this->respond($notifications);
    }

    public function clearNotification(int $notificationId)
    {
        return $this->notificationRepository->clearNotification($notificationId)
            ? $this->respond(['status' => 'cleared'])
            : $this->fail('Failed to clear notification.');
    }

    public function clearAllNotifications(int $userId)
    {
        return $this->notificationRepository->clearAllNotifications($userId)
            ? $this->respond(['status' => 'cleared'])
            : $this->fail('Failed to clear notifications.');
    }

    public function delete($id = null)
    {
        if ($this->notificationRepository->delete((int) $id))
        {
            return $this->respondDeleted(['status' => 'deleted']);
        }

        return $this->failNotFound('Notification not found or could not be deleted.');
    }
}