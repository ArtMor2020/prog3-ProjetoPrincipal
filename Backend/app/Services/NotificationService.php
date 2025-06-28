<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\DirectMessageRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Entities\CommentEntity;
use App\Entities\PostEntity;
use App\Repositories\CommunityJoinRequestRepository;

class NotificationService
{
    private NotificationRepository $notificationRepository;
    private PostRepository $postRepository;
    private DirectMessageRepository $directMessageRepository;
    private UserRepository $userRepository;
    private CommunityRepository $communityRepository;
    private CommentEntity $commentEntity;
    private PostEntity $postEntity;
    private CommunityJoinRequestRepository $joinRequestRepository;
    

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository();
        $this->postRepository = new PostRepository();
        $this->directMessageRepository = new DirectMessageRepository();
        $this->userRepository = new UserRepository();
        $this->communityRepository = new CommunityRepository();
        $this->postEntity = new PostEntity();
        $this->joinRequestRepository = new CommunityJoinRequestRepository();
    }

    public function notifyAboutReportedPost(PostEntity $post): bool
    {
        $targetUserIds = [];

        if ($post->getIdCommunity()) {
            $userInCommunityRepo = new \App\Repositories\UserInCommunityRepository();
            $admins = $userInCommunityRepo->listAdministratorsByCommunity($post->getIdCommunity());
            foreach ($admins as $admin) {
                if ($admin->getIdUser() != $post->getIdUser()) {
                    $targetUserIds[] = $admin->getIdUser();
                }
            }
        } else {
            if ($post->getIdUser() != 1) { 
                $targetUserIds[] = 1; 
            }
        }
        
        if (empty($targetUserIds)) return true; 

        foreach (array_unique($targetUserIds) as $userId) {
            $this->notificationRepository->notifyUser($userId, 'post_report', $post->getId());
        }

        return true;
    }

    public function notifyAboutReportedComment(CommentEntity $comment): bool
    {
        $post = $this->postRepository->findById($comment->getIdParentPost());
        if (!$post) return false;

        $targetUserIds = [];

        if ($post->getIdCommunity()) {
            $userInCommunityRepo = new \App\Repositories\UserInCommunityRepository();
            $admins = $userInCommunityRepo->listAdministratorsByCommunity($post->getIdCommunity());
            foreach ($admins as $admin) {
                $targetUserIds[] = $admin->getIdUser();
            }
        } 
        else {
            $targetUserIds[] = $post->getIdUser();
        }
        
        if (empty($targetUserIds)) return false;

        foreach (array_unique($targetUserIds) as $userId) {
            $this->notificationRepository->notifyUser($userId, 'comment_report', $comment->getId());
        }

        return true;
    }

    public function getNotifications(int $userId): array
    {
        try {
            $rawNotifications = $this->notificationRepository->findNotificationsForUser($userId);
            $notifications = [];

            foreach ($rawNotifications as $rawNotification) {
                $notificationData = null;
                $originId = (int)$rawNotification->id_origin; 

                switch ($rawNotification->type) {
                    case 'mention_in_post':
                    case 'post_report':
                        $post = $this->postRepository->findById($originId);
                        if ($post) {
                            $text = ($rawNotification->type === 'post_report') 
                                ? "O post \"{$post->getTitle()}\" foi reportado." 
                                : "Você foi mencionado no post \"{$post->getTitle()}\".";
                            $notificationData = ['id' => $rawNotification->id, 'type' => $rawNotification->type, 'text' => $text, 'target_id' => $post->getId()];
                        }
                        break;
                    
                    case 'mention_in_comment':
                        $commentRepo = new \App\Repositories\CommentRepository();
                        $comment = $commentRepo->findById($originId);
                        if ($comment) {
                            $post = $this->postRepository->findById($comment->getIdParentPost());
                            if ($post) {
                                $notificationData = ['id' => $rawNotification->id, 'type' => $rawNotification->type, 'text' => "Você foi mencionado em um comentário no post: \"{$post->getTitle()}\"", 'target_id' => $post->getId()];
                            }
                        }
                        break;
                    
                    case 'friend_request':
                        $requester = $this->userRepository->getUserById($originId);
                        if ($requester) {
                            $notificationData = ['id' => $rawNotification->id, 'type' => $rawNotification->type, 'text' => "Novo pedido de amizade de u/{$requester->getName()}.", 'target_id' => $originId];
                        }
                        break;
                    
                    case 'community_join_request':
                        $request = $this->joinRequestRepository->getRequest($originId);
                        if ($request) {
                            $requester = $this->userRepository->getUserById($request->id_user);
                            $community = $this->communityRepository->findById($request->id_community);

                            if ($requester && $community) {
                                $notificationData = [
                                    'id' => $rawNotification->id,
                                    'type' => $rawNotification->type,
                                    'text' => "u/{$requester->getName()} pediu para entrar em r/{$community->getName()}",
                                    'target_id' => $community->getId(),
                                    'target_type' => 'community'
                                ];
                            }
                        }
                        break;
                }

                if ($notificationData) {
                    $notifications[] = $notificationData;
                }
            }
            
            return $notifications;
        } catch (Throwable $e) {
            log_message('error', '[NotificationService] Exceção: ' . $e->getMessage());
            return [];
        }
    }
}