<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\UserInCommunityRepository;

class UserInCommunityController extends ResourceController
{
    protected $format = 'json';
    protected UserInCommunityRepository $repo;

    public function __construct()
    {
        $this->repo = new UserInCommunityRepository();
    }

    public function byCommunity($communityId = null)
    {
        if (!is_numeric($communityId)) {
            return $this->failValidationError('Community ID inválido.');
        }
        $members = $this->repo->listByCommunity((int) $communityId);
        return $this->respond(array_map(fn($e) => $e->toArray(), $members));
    }

    public function byUser($userId = null)
    {
        if (!is_numeric($userId)) {
            return $this->failValidationError('User ID inválido.');
        }
        $list = $this->repo->listByUser((int) $userId);
        return $this->respond(array_map(fn($e) => $e->toArray(), $list));
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        foreach (['community_id', 'user_id'] as $f) {
            if (empty($data[$f]) || !is_numeric($data[$f])) {
                return $this->failValidationError("$f é obrigatório e deve ser numérico.");
            }
        }
        $role = $data['role'] ?? 'member';
        $ok = $this->repo->addMember((int) $data['community_id'], (int) $data['user_id'], $role);
        return $ok
            ? $this->respondCreated(['status' => 'joined'])
            : $this->fail('Membro já existe ou erro na inscrição', 400);
    }

    public function delete($communityId = null, $userId = null)
    {
        if (!is_numeric($communityId) || !is_numeric($userId)) {
            return $this->failValidationError('IDs inválidos.');
        }
        $ok = $this->repo->removeMember((int) $communityId, (int) $userId);
        return $ok
            ? $this->respondDeleted(['status' => 'removed'])
            : $this->failNotFound('Associação não encontrada');
    }

    public function role($communityId = null, $userId = null)
    {
        $data = $this->request->getJSON(true);
        if (empty($data['role'])) {
            return $this->failValidationError('Campo role é obrigatório.');
        }
        $ok = $this->repo->updateRole((int) $communityId, (int) $userId, $data['role']);
        return $ok
            ? $this->respond(['status' => 'role updated'])
            : $this->failNotFound('Associação não encontrada');
    }

    public function ban($communityId = null, $userId = null)
    {
        $ok = $this->repo->banMember((int) $communityId, (int) $userId);
        return $ok
            ? $this->respond(['status' => 'banned'])
            : $this->failNotFound('Associação não encontrada');
    }

    public function unban($communityId = null, $userId = null)
    {
        $ok = $this->repo->unbanMember((int) $communityId, (int) $userId);
        return $ok
            ? $this->respond(['status' => 'unbanned'])
            : $this->failNotFound('Associação não encontrada');
    }
}
