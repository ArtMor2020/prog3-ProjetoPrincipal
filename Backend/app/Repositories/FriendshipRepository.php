<?php

namespace App\Repositories;

use App\Models\FriendshipModel;
use App\Entities\FriendshipEntity;

class FriendshipRepository
{
    protected FriendshipModel $model;

    public function __construct()
    {
        $this->model = new FriendshipModel();
    }

    public function findAll(): array
    {
        return $this->model->findAll();
    }

    public function findFriendship(int $id)
    {
        return $this->model->find($id);
    }

    public function findFriendsForUser(int $userId)
    {
        return $this->model
            ->where('status', 'friends')
            ->groupStart()
            ->where('id_user1', $userId)
            ->orWhere('id_user2', $userId)
            ->groupEnd()
            ->findAll();
    }

    public function findFriendRequestsForUser(int $userId)
    {
        return $this->model
            ->where('id_user2', $userId)
            ->where('status', 'friend_request')
            ->findAll();
    }

    public function findFriendshipBetweenUsers(int $user1, int $user2): ?FriendshipEntity
    {
        return $this->model
            ->groupStart()
            ->where('id_user1', $user1)->where('id_user2', $user2)
            ->groupEnd()
            ->orGroupStart()
            ->where('id_user1', $user2)->where('id_user2', $user1)
            ->groupEnd()
            ->first();
    }

    public function createFriendRequest(int $user1, int $user2)
    {
        if ($this->findFriendshipBetweenUsers($user1, $user2)) {
            return false;
        }

        $data = [
            'id_user1' => $user1,
            'id_user2' => $user2,
            'status' => 'friend_request',
            'requested_at' => date('Y-m-d H:i:s'),
            'friends_since' => null,
        ];

        return $this->model->insert($data, true);
    }

    public function acceptFriendsRequest(int $friendshipId): bool
    {
        $this->model
            ->where('id', $friendshipId)
            ->where('status', 'friend_request')
            ->set(['status' => 'friends', 'friends_since' => date('Y-m-d H:i:s')])
            ->update();

        return $this->model->db->affectedRows() > 0;
    }

    public function deleteFriendship(int $friendshipId): bool
    {
        return (bool) $this->model->delete($friendshipId);
    }
}