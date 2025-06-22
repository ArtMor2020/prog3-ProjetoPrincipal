<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\DirectMessageRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

class NotificationService
{
    private NotificationRepository $notificationRepository;
    private PostRepository $postRepository;
    private DirectMessageRepository $directMessageRepository;
    private UserRepository $userRepository;
    private CommunityRepository $communityRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository();
        $this->postRepository = new PostRepository();
        $this->directMessageRepository = new DirectMessageRepository();
        $this->userRepository = new UserRepository();
        $this->communityRepository = new CommunityRepository();
    }

    public function getNotifications(int $userId)
    {
        $rawNotifications = $this->notificationRepository->findNotificationsForUser($userId);
        $notifications = [];

        foreach ($rawNotifications as $rawNotification) {
            $notificationData = null;
            $originId = $rawNotification->id_origin;
            $notificationId = $rawNotification->id;
            $notificationType = $rawNotification->type;

            switch ($notificationType) {
                case 'mention_in_post':
                    $post = $this->postRepository->findById($originId);
                    if ($post) {
                        $notificationData = [
                            'id' => $notificationId,
                            'type' => $notificationType,
                            'text' => "Você foi mencionado em um post: \"{$post->getTitle()}\"",
                            'post_id' => $post->getId()
                        ];
                    }
                    break;

                case 'mention_in_comment':
                    $commentRepo = new \App\Repositories\CommentRepository();
                    $comment = $commentRepo->findById($originId);
                    if ($comment) {
                        $post = $this->postRepository->findById($comment->getIdParentPost());
                        if ($post) {
                            $notificationData = [
                                'id' => $notificationId,
                                'type' => $notificationType,
                                'text' => "Você foi mencionado em um comentário no post: \"{$post->getTitle()}\"",
                                'post_id' => $post->getId()
                            ];
                        }
                    }
                    break;

                case 'message':
                    $sender = $this->userRepository->getUserById($originId);
                    if ($sender) {
                        $notificationData = [
                            'id' => $notificationId,
                            'type' => $notificationType,
                            'text' => "Você recebeu uma nova mensagem de {$sender->getName()}",
                            'user_id' => $sender->getId()
                        ];
                    }
                    break;

                case 'pending_post':
                    $community = $this->communityRepository->findById($originId);
                    if ($community) {
                        $notificationData = [
                            'id' => $notificationId,
                            'type' => $notificationType,
                            'text' => "Há posts pendentes na comunidade \"{$community->getName()}\"",
                            'community_id' => $community->getId()
                        ];
                    }
                    break;

                case 'friend_request':
                    $requester = $this->userRepository->getUserById($originId);
                    if ($requester) {
                        $notificationData = [
                            'id' => $notificationId,
                            'type' => $notificationType,
                            'text' => "Novo pedido de amizade de \"{$requester->getName()}\"",
                            'user_id' => $requester->getId()
                        ];
                    }
                    break;

                case 'invite':
                    $community = $this->communityRepository->findById($originId);
                    if ($community) {
                        $notificationData = [
                            'id' => $notificationId,
                            'type' => $notificationType,
                            'text' => "Você foi convidado para a comunidade \"{$community->getName()}\"",
                            'community_id' => $community->getId()
                        ];
                    }
                    break;
            }

            if ($notificationData) {
                $notifications[] = $notificationData;
            }
        }

        return $notifications;
    }
}