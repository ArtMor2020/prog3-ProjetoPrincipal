<?php

namespace App\Controllers;

use App\Database\Migrations\Community;
use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommunityJoinRequestRepository;
use App\Services\CommunityJoinRequestService;

class CommunityJoinRequestController extends ResourceController
{
    protected $format = 'json';
    protected CommunityJoinRequestRepository $repository;
    protected CommunityJoinRequestService $communityJoinRequestService;

    public function __construct()
    {
        $this->repository = new CommunityJoinRequestRepository();
        $this->communityJoinRequestService = new CommunityJoinRequestService();
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

    public function approve($id)
    {
        $ok = $this->communityJoinRequestService->acceptRequest((int) $id);
        if (!$ok) {
            return $this->fail('Cannot approve request', 400);
        }
        return $this->respond(['status' => 'approved']);
    }

    public function reject($id)
    {
        $ok = $this->repository->reject((int) $id);
        return $ok ? $this->respond(['message' => 'Rejected']) : $this->fail('Cannot reject', 400);
    }
}