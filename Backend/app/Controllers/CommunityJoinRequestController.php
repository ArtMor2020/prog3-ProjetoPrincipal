<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommunityJoinRequestRepository;
use App\Services\CommunityJoinRequestService;

class CommunityJoinRequestController extends ResourceController
{
    protected $format = 'json';

    /** @var CommunityJoinRequestRepository */
    protected $repository;

    /** @var CommunityJoinRequestService */
    protected $service;

    public function __construct()
    {
        $this->repository = new CommunityJoinRequestRepository();
        $this->service    = new CommunityJoinRequestService();
    }

    /**
     * GET /community-join-requests
     */
    public function index()
    {
        $all = $this->repository->listAll();
        return $this->respond($all);
    }

    /**
     * GET /community-join-requests/community/{communityId}
     */
    public function byCommunity($communityId)
    {
        $list = $this->repository->listByCommunity((int) $communityId);
        return $this->respond($list);
    }

    /**
     * GET /community-join-requests/user/{userId}
     */
    public function byUser($userId)
    {
        $list = $this->repository->listByUser((int) $userId);
        return $this->respond($list);
    }

    /**
     * POST /community-join-requests
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['community_id']) || empty($data['user_id'])) {
            return $this->failValidationError('community_id e user_id são obrigatórios.');
        }

        $ok = $this->service->sendRequest(
            (int)$data['community_id'],
            (int)$data['user_id']
        );

        return $ok
            ? $this->respondCreated(['message' => 'Request created'])
            : $this->fail('Already requested or invalid', 400);
    }

    /**
     * PUT /community-join-requests/{id}/approve
     */
    public function approve($id)
    {
        $ok = $this->service->acceptRequest((int)$id);
        if (! $ok) {
            return $this->fail('Cannot approve request', 400);
        }
        return $this->respond(['status' => 'approved']);
    }

    /**
     * PUT /community-join-requests/{id}/reject
     */
    public function reject($id)
    {
        $ok = $this->service->rejectRequest((int)$id);
        if (! $ok) {
            return $this->fail('Cannot reject request', 400);
        }
        return $this->respond(['status' => 'rejected']);
    }
}
