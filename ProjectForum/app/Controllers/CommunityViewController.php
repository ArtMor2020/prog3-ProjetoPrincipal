<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommunityViewRepository;

class CommunityViewController extends ResourceController
{
    protected $format = 'json';
    protected CommunityViewRepository $repo;

    public function __construct()
    {
        $this->repo = new CommunityViewRepository();
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['community_id']) || empty($data['user_id'])) {
            return $this->failValidationError('community_id e user_id são obrigatórios.');
        }

        $ok = $this->repo->addView(
            (int) $data['community_id'],
            (int) $data['user_id']
        );

        return $ok
            ? $this->respondCreated(['status' => 'view recorded'])
            : $this->fail('Não foi possível registrar a visualização.', 500);
    }

    public function byCommunity($communityId = null)
    {
        if (!is_numeric($communityId)) {
            return $this->failValidationError('Community ID inválido.');
        }

        $views = $this->repo->listByCommunity((int) $communityId);
        return $this->respond(array_map(fn($e) => $e->toArray(), $views));
    }

    public function byUser($userId = null)
    {
        if (!is_numeric($userId)) {
            return $this->failValidationError('User ID inválido.');
        }

        $views = $this->repo->listByUser((int) $userId);
        return $this->respond(array_map(fn($e) => $e->toArray(), $views));
    }
}
