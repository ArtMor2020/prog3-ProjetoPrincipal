<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommunityJoinRequestRepository;

class CommunityJoinRequestController extends ResourceController
{
    protected $format = 'json';
    protected CommunityJoinRequestRepository $repository;

    public function __construct()
    {
        $this->repository = new CommunityJoinRequestRepository();
    }

    public function index()
    {
        return $this->respond($this->repository->listAll());
    }

    public function byCommunity($communityId)
    {
        return $this->respond($this->repository->listByCommunity((int) $communityId));
    }

    public function byUser($userId)
    {
        return $this->respond($this->repository->listByUser((int) $userId));
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $ok = $this->repository->create($data['community_id'], $data['user_id']);
        return $ok
            ? $this->respondCreated(['message' => 'Request created'])
            : $this->fail('Already requested or invalid', 400);
    }

    public function approve($communityId, $userId)
    {
        $ok = $this->repository->approve((int) $communityId, (int) $userId);
        if (!$ok) {
            return $this->fail('Cannot approve request', 400);
        }
        return $this->respond(['status' => 'approved']);
    }

    public function reject($communityId, $userId)
    {
        $ok = $this->repository->reject((int) $communityId, (int) $userId);
        return $ok ? $this->respond(['message' => 'Rejected']) : $this->fail('Cannot reject', 400);
    }
}