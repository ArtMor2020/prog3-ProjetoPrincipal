<?php

namespace App\Services;

use App\Repositories\DirectMessageRepository;
use App\Repositories\NotificationRepository;

class DirectMessageService
{
    private DirectMessageRepository $directMessageRepository;
    private NotificationRepository $notificationRepository;

    public function __construct()
    {
        $this->directMessageRepository = new DirectMessageRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    public function sendMessage(array $data): int
    {        
        $isAlreadyNotified = $this->notificationRepository
            ->existsUnreadNotification($data['id_reciever'], $data['id_sender'], 'message');

        if(!$isAlreadyNotified){
            $this->notificationRepository->notifyUser($data['id_reciever'], 'message', $data['id_sender']);
        }

        return $this->directMessageRepository->sendMessage($data);
    }
}