<?php

namespace App\Services;

use App\Repositories\CommunityJoinRequestRepository;
use App\Repositories\UserInCommunityRepository;
use App\Entities\CommunityJoinRequestEntity;

class CommunityJoinRequestService
{
    protected CommunityJoinRequestRepository $communityJoinRequestRepository;
    protected UserInCommunityRepository $userInCommunityRepository;

    public function __construct()
    {
        $this->communityJoinRequestRepository = new CommunityJoinRequestRepository();
        $this->userInCommunityRepository = new UserInCommunityRepository();
    }

    public function makeRequest(int $communityId, int $userId)
    {
        return $this->communityJoinRequestRepository->create($communityId, $userId);
    }

    public function acceptRequest(int $idRequest)
    {
        try{
            $this->communityJoinRequestRepository->approve($idRequest);
            
            $data = $this->communityJoinRequestRepository->getRequest($idRequest);

            $this->userInCommunityRepository->addMember($data['id_community'], $data['id_user']);

            return true;

        } catch (\Throwable $e) {
            log_message('error', 'CommunityJoinRequestService::acceptRequest - ' . $e->getMessage());

            return false;
        }
    }
    
    public function rejectRequest(int $idRequest)
    {
        return $this->communityJoinRequestRepository->reject($idRequest);
    }
}