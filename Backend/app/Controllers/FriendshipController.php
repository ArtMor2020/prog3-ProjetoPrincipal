<?php

namespace App\Controllers;

use App\Repositories\FriendshipRepository;
use App\Services\FriendshipService;
use CodeIgniter\RESTful\ResourceController;

class FriendshipController extends ResourceController
{
    protected FriendshipRepository $friendshipRepository;
    protected FriendshipService $friendshipService;

    public function __construct()
    {
        $this->friendshipRepository = new FriendshipRepository();
        $this->friendshipService = new FriendshipService();
    }

    public function getStatus($userA, $userB)
    {
        $friendship = $this->friendshipRepository->findFriendshipBetweenUsers((int) $userA, (int) $userB);

        if (!$friendship) {
            return $this->respond(['status' => 'not_friends']);
        }

        if ($friendship->status === 'friends') {
            return $this->respond(['status' => 'friends']);
        }

        if ($friendship->id_user1 == $userA) {
            return $this->respond(['status' => 'request_sent']);
        } else {
            return $this->respond(['status' => 'request_received', 'request_id' => $friendship->id]);
        }
    }

    public function sendRequest()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_user1']) || empty($data['id_user2'])) {
            return $this->failValidationError('Campos id_user1 e id_user2 são obrigatórios.');
        }

        $id = $this->friendshipService->createFriendRequest($data['id_user1'], $data['id_user2']);

        if ($id === false) {
            return $this->fail('Não foi possível enviar o pedido.', 400);
        }

        return $this->respondCreated(['id' => $id]);
    }

    public function getRequests($userId)
    {
        $requests = $this->friendshipRepository->findFriendRequestsForUser($userId);

        return $this->respond($requests);
    }

    public function acceptRequest($requestId)
    {
        $success = $this->friendshipRepository->acceptFriendsRequest($requestId);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed', 400);
    }

    public function refuseRequest($requestId)
    {
        $success = $this->friendshipRepository->deleteFriendship($requestId);

        if (!$success) {
            $exists = (bool) $this->friendshipRepository->findFriendship((int) $requestId);

            return $exists
                ? $this->fail('Amizade já apagada ou não pode ser apagada', 400)
                : $this->failNotFound('Amizade não encontrada');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }

    public function getFriends($userId)
    {
        $friends = $this->friendshipRepository->findFriendsForUser($userId);

        return $this->respond($friends);
    }
}