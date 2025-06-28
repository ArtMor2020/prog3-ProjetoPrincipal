<?php

namespace App\Services;

use App\Repositories\CommunityJoinRequestRepository;
use App\Repositories\UserInCommunityRepository;
use App\Repositories\NotificationRepository;
use Throwable;

class CommunityJoinRequestService
{
    protected CommunityJoinRequestRepository $communityJoinRequestRepository;
    protected UserInCommunityRepository $userInCommunityRepository;
    protected NotificationRepository $notificationRepository;

    public function __construct()
    {
        $this->communityJoinRequestRepository = new CommunityJoinRequestRepository();
        $this->userInCommunityRepository = new UserInCommunityRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    public function makeRequest(int $communityId, int $userId)
    {
        $requestId = $this->communityJoinRequestRepository->create($communityId, $userId);
        if (!$requestId) {
            return false;
        }

        try {
            $admins = $this->userInCommunityRepository->listAdministratorsByCommunity($communityId);
            if (empty($admins)) {
                return $requestId;
            }

            foreach ($admins as $admin) {
                $adminId = $admin->getIdUser();
                $originId = $requestId;
                
                if (!$this->notificationRepository->existsUnreadNotification($adminId, $originId, 'community_join_request')) {
                    $this->notificationRepository->notifyUser($adminId, 'community_join_request', $originId);
                }
            }
        } catch (Throwable $e) {
            log_message('error', '[CJR_Service] Falha ao notificar admins: ' . $e->getMessage());
        }

        return $requestId;
    }

    public function acceptRequest(int $requestId)
    {
        try {
            $request = $this->communityJoinRequestRepository->getRequest($requestId);
            if (!$request) {
                return false;
            }
            $this->communityJoinRequestRepository->approve($requestId);
            $this->userInCommunityRepository->addMember($request->id_community, $request->id_user);
            return true;
        } catch (Throwable $e) {
            log_message('error', 'CommunityJoinRequestService::acceptRequest - ' . $e->getMessage());
            return false;
        }
    }
    
    public function rejectRequest(int $requestId): bool
    {
        return $this->communityJoinRequestRepository->reject($requestId);
    }
}