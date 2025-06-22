<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Services\CommunityJoinRequestService;

class CommunityJoinRequestController extends ResourceController
{
    protected $format = 'json';
    protected CommunityJoinRequestService $service;

    public function __construct()
    {
        $this->service = new CommunityJoinRequestService();
    }

    public function index()
    {
        $repo = new \App\Repositories\CommunityJoinRequestRepository();
        $all = $repo->listAll();
        return $this->respond($all);
    }

    public function byCommunity($communityId)
    {
        $repo = new \App\Repositories\CommunityJoinRequestRepository();
        $list = $repo->listByCommunity((int) $communityId);
        return $this->respond($list);
    }

    public function byUser($userId)
    {
        $repo = new \App\Repositories\CommunityJoinRequestRepository();
        $list = $repo->listByUser((int) $userId);
        return $this->respond($list);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['community_id']) || empty($data['user_id'])) {
            return $this->failValidationError('community_id e user_id são obrigatórios.');
        }

        $ok = $this->service->makeRequest(
            (int) $data['community_id'],
            (int) $data['user_id']
        );
        // --------------------------------

        return $ok
            ? $this->respondCreated(['message' => 'Request created', 'request_id' => $ok])
            : $this->fail('Pedido já existe ou dados são inválidos.', 400);
    }

    public function approve($id)
    {
        $ok = $this->service->acceptRequest((int) $id);
        if (!$ok) {
            return $this->fail('Não foi possível aprovar o pedido.', 400);
        }
        return $this->respond(['status' => 'approved']);
    }

    public function reject($id)
    {
        $ok = $this->service->rejectRequest((int) $id);
        if (!$ok) {
            return $this->fail('Não foi possível rejeitar o pedido.', 400);
        }
        return $this->respond(['status' => 'rejected']);
    }
}