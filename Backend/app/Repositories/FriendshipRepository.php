<?php

namespace App\Repositories;

use App\Models\FriendshipModel;
use App\Entities\FriendshipEntity;
use DateTime;

class FriendshipRepository
{
    protected FriendshipModel $model;

    public function __construct(){
        $this->model = New FriendshipModel();
    }

    public function findAll(): array {
        return $this->model->findAll();
    }

    public function findFriendship(int $id) 
    {
        return $this->model->where('id', $id)
                            ->findAll();
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

    public function createFriendRequest(int $user1, int $user2){
        
        $isAlreadyFriend = $this->model
            ->groupStart()
                ->where('id_user1', $user1)
                ->where('id_user2', $user2)
            ->groupEnd()
            ->orGroupStart()
                ->where('id_user1', $user2)
                ->where('id_user2', $user1)
            ->groupEnd()
            ->first();
        
        if($isAlreadyFriend) return false;

        $data = [
            'id_user1'      => $user1,
            'id_user2'      => $user2,
            'status'        => 'friend_request',
            'requested_at'  => date('Y-m-d H:i:s'),
            'friends_since' => null,
        ];

        return $this->model->insert($data);
    }

    // not needed
    /* public function updateFriendship(int $user1, int $user2, array $data)
    {
        return $this->model
            ->groupStart()
                ->where('id_user1', $user1)
                ->where('id_user2', $user2)
            ->groupEnd()
            ->orGroupStart()
                ->where('id_user1', $user2)
                ->where('id_user2', $user1)
            ->groupEnd()
            ->set($data)
            ->update();
    } */

    public function acceptFriendsRequest(int $friendshipId): bool
    {
        $this->model
            ->where('id', $friendshipId)
            ->where('status', 'friend_request')
            ->set([
                'status' => 'friends',
                'friends_since' => date('Y-m-d H:i:s')
            ])
            ->update();

        return $this->model->db->affectedRows() > 0;
    }

    public function deleteFriendship(int $friendshipId): bool 
    {
        $this->model
            ->where('id', $friendshipId)
            ->delete();

        return $this->model->db->affectedRows() > 0;
    }
}