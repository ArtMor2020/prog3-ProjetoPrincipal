<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\UserInCommunityRepository;
use Throwable;

class CommunityService
{
    protected CommunityRepository $communityRepository;
    protected UserInCommunityRepository $userInCommunityRepository;

    public function __construct()
    {
        $this->communityRepository = new CommunityRepository();
        $this->userInCommunityRepository = new UserInCommunityRepository();
    }

    public function createCommunity(array $data): int|false
    {

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $communityId = $this->communityRepository->createCommunity($data);

            if (!$communityId) {

                $db->transRollback();
                return false;
            }

            $ownerId = $data['id_owner'];
            $this->userInCommunityRepository->addMember($communityId, $ownerId, 'ADMIN');

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', '[CommunityService] Falha na transação ao criar comunidade.');
                return false;
            }

            return $communityId;

        } catch (Throwable $e) {
            $db->transRollback();
            log_message('error', '[CommunityService] Exceção ao criar comunidade: ' . $e->getMessage());
            return false;
        }
    }
}