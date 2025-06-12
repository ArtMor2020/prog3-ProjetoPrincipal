<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\BlockedUserRepository;
use App\Entities\BlockedUserEntity;

class BlockedUserController extends ResourceController
{
    protected $format = 'json';
    protected BlockedUserRepository $repository;

    public function __construct()
    {
        $this->repository = new BlockedUserRepository();
    }

    public function block()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_user']) || empty($data['id_blocked_user'])) {
            return $this->failValidationError('Campos id_user e id_blocked_user são obrigatórios.');
        }

        $entity = new \App\Entities\BlockedUserEntity();
        $entity->setIdUser((int) $data['id_user'])
            ->setIdBlockedUser((int) $data['id_blocked_user']);

        $success = $this->repository->blockUser($entity);
        if ($success) {
            return $this->respond(['status' => 'blocked']);
        } else {
            return $this->fail('Block failed');
        }


        return $success
            ? $this->respondCreated(['status' => 'blocked'])
            : $this->fail('Block failed');
    }

    public function unblock()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_user']) || empty($data['id_blocked_user'])) {
            return $this->failValidationError('Campos id_user e id_blocked_user são obrigatórios.');
        }

        $success = $this->repository->unblockUser(
            (int) $data['id_user'],
            (int) $data['id_blocked_user']
        );

        return $success
            ? $this->respondDeleted(['status' => 'unblocked'])
            : $this->fail('Unblock failed');
    }

    public function index($userId = null)
    {
        if ($userId === null) {
            return $this->fail('User ID required');
        }

        $blocked = $this->repository->getBlockedUsers((int) $userId);

        return $blocked !== false
            ? $this->respond($blocked)
            : $this->fail('Could not fetch blocked users');
    }
}
