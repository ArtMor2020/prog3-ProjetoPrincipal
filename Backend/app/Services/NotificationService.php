<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\NotificationRepository;
use App\Entities\NotificationEntity;
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

    public function __construct(){
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

        foreach ( $rawNotifications as $rawNotification)
        {

            switch( $rawNotification['type'] ) {
                case 'mention_in_post':

                    $post = $this->postRepository->getPost($rawNotification['id_origin']);

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Voce foi mencionado em um post! \"{$post->getTitle()}\"",
                        'post_id' => $post->getId()
                    ];

                    break;

                case 'mention_in_comment':

                    $post = $this->postRepository->getPost($rawNotification['id_origin']);

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Voce foi mencionado em um comentario nesse post! \"{$post->getTitle()}\"",
                        'post_id' => $post->getId()
                    ];

                    break;

                case 'message':

                    $message = $this->directMessageRepository->getMessage($rawNotification['id_origin']);
                    $user = $this->userRepository->getUserById($message->getIdSender());

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Voce foi recebeu uma mensagem de {$user->getName()}",
                        'user_id' => $user->getId()
                    ];

                    break;

                case 'pending_post':

                    $post = $this->postRepository->getPost($rawNotification['id_origin']);
                    $community = $this->communityRepository->findById($post->getIdCommunity());

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Posts pendentes na comunidade \"{$post->getTitle()}\"",
                        'community_id' => $post->getIdCommunity()
                    ];

                    break;

                case 'friend_request':

                    $user = $this->userRepository->getUserById($rawNotification['id_origin']);

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Novo pedido de amizade de \"{$user->getName()}\"",
                        'user_id' => $user->getId()
                    ];

                    break;
                
                case 'invite':

                    $community = $this->communityRepository->findById($rawNotification['id_origin']);

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Voce foi convidado para a comunidade \"{$community->getName()}\"",
                        'community_id' => $community->getId()
                    ];

                    break;

                default:

                    $notifications[] = [
                        'id'   => $rawNotification['id'],
                        'type' => $rawNotification['type'],
                        'text' => "Tipo \"{$rawNotification['type']}\" desconhecido.",
                        'origin_id' => $rawNotification['id_origin'],
                    ];
                    
                    break;
            }
        }
        
        return $notifications;
    }
}