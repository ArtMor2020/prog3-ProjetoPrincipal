<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommunityRepository;
use App\Services\CommunityService;

class CommunityController extends ResourceController
{
    protected $format = 'json';
    protected CommunityRepository $repository;
    protected CommunityService $service;

    public function __construct()
    {
        $this->repository = new CommunityRepository();
        $this->service = new CommunityService();
    }

    public function index()
    {
        $comms = $this->repository->findAll();
        return $this->respond($comms);
    }

    public function show($id = null)
    {
        $comm = $this->repository->findById((int) $id);
        return $comm
            ? $this->respond($comm)
            : $this->failNotFound('Community not found');
    }

    public function byOwner($ownerId = null)
    {
        if (empty($ownerId) || !is_numeric($ownerId)) {
            return $this->failValidationError('Owner ID inválido.');
        }

        $list = $this->repository->findByOwner((int) $ownerId);
        $data = array_map(fn($e) => $e->toArray(), $list);

        return $this->respond($data);
    }

    public function search($term = null)
    {
        if (empty($term)) {
            return $this->failValidationError('Termo de busca obrigatório.');
        }

        $list = $this->repository->searchByName($term);
        $data = array_map(fn($e) => $e->toArray(), $list);

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (empty($data['name']) || empty($data['description']) || empty($data['id_owner'])) {
            return $this->failValidationError('name, description e id_owner são obrigatórios.');
        }

        $payload = [
            'name' => $data['name'],
            'description' => $data['description'],
            'id_owner' => (int) $data['id_owner'],
            'is_private' => (bool) ($data['is_private'] ?? false),
        ];

        $id = $this->service->createCommunity($payload);

        if ($id === false) {
            return $this->fail('Não foi possível criar a comunidade. O nome pode já existir.', 409);
        }

        return $this->respondCreated(['id' => $id]);
        // -------------------------
    }
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->failValidationError('No data received.');
        }

        $success = $this->repository->updateCommunity((int) $id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed', 400);
    }

    public function delete($id = null)
    {
        $success = $this->repository->deleteCommunity((int) $id);

        if (!$success) {
            $exists = (bool) $this->repository->findById((int) $id);
            return $exists
                ? $this->fail('Community already deleted or cannot be deleted', 400)
                : $this->failNotFound('Community not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }

    public function ban($id = null)
    {
        $comm = $this->repository->findById((int) $id);
        if (!$comm) {
            return $this->failNotFound('Community not found');
        }
        if ($comm->getIsBanned()) {
            return $this->fail('Community already banned', 400);
        }

        $success = $this->repository->banCommunity((int) $id);
        return $success
            ? $this->respond(['status' => 'banned'])
            : $this->fail('Ban failed', 500);
    }

    public function unban($id = null)
    {
        $comm = $this->repository->findById((int) $id);
        if (!$comm) {
            return $this->failNotFound('Community not found');
        }
        if (!$comm->getIsBanned()) {
            return $this->fail('Community is not banned', 400);
        }

        $success = $this->repository->unbanCommunity((int) $id);
        return $success
            ? $this->respond(['status' => 'unbanned'])
            : $this->fail('Unban failed', 500);
    }

    public function restore($id = null)
    {
        $comm = $this->repository->findById((int) $id);
        if (!$comm) {
            return $this->failNotFound('Community not found');
        }
        if (!$comm->getIsDeleted()) {
            return $this->fail('Community is not deleted', 400);
        }

        $success = $this->repository->restoreCommunity((int) $id);
        return $success
            ? $this->respond(['status' => 'restored'])
            : $this->fail('Restore failed', 500);
    }

    public function userStatus($communityId, $userId)
    {
        $userInCommunityRepo = new \App\Repositories\UserInCommunityRepository();
        $joinRequestRepo = new \App\Repositories\CommunityJoinRequestRepository();

        $membership = $userInCommunityRepo->getMembership((int) $communityId, (int) $userId);
        if ($membership) {
            if ($membership->is_banned)
                return $this->respond(['status' => 'banned']);
            return $this->respond(['status' => $membership->role]);
        }

        $request = $joinRequestRepo->getPendingRequest((int) $communityId, (int) $userId);
        if ($request) {
            return $this->respond(['status' => 'request_sent']);
        }

        return $this->respond(['status' => 'not_member']);
    }
}