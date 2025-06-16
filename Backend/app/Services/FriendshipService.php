<?php

namespace App\Services;

use App\Database\Migrations\Notification;
use App\Repositories\FriendshipRepository;
use App\Repositories\NotificationRepository;

class FriendshipService
{
    private FriendshipRepository $friendshipRepository;
    private NotificationRepository $notificationRepository;

    public function __construct(){
        $this->friendshipRepository = new FriendshipRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    public function createFriendRequest(int $user1, int $user2)
    {

        if( !$this->notificationRepository->existsUnreadNotification(
            $user2, 
            $user1, 
            'friend_request'
        ))
        {
            $this->notificationRepository->notifyUser($user2, 'friend_request', $user1);
        }

        return $this->friendshipRepository->createFriendRequest($user1, $user2);
    }
}