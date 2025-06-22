<?php

namespace App\Services;

use App\Repositories\NotificationRepository;
use App\Repositories\UserInCommunityRepository;

class InviteService
{
    private UserInCommunityRepository $userInCommunityRepository;
    private NotificationRepository $notificationRepository;

    public function __construct()
    {
        $this->userInCommunityRepository = new UserInCommunityRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    function inviteUserToCommunity(int $userId, int $communityId): bool
    {
        if (
            !$this->notificationRepository->existsUnreadNotification(
                $userId,
                $communityId,
                'invite'
            )
        ) {
            $this->notificationRepository->notifyUser(
                $userId,
                'invite',
                $communityId
            );
        }

        return $this->userInCommunityRepository->inviteUserToCommunity($communityId, $userId);
    }
}