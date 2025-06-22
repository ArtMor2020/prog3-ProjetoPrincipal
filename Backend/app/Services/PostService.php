<?php

namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\AttachmentInPostRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserInCommunityRepository;
use App\Repositories\UserRepository;
use CodeIgniter\HTTP\Files\UploadedFile;
use Throwable;

class PostService
{
    protected PostRepository $postRepository;
    protected AttachmentInPostRepository $attachmentInPostRepository;
    protected AttachmentRepository $attachmentRepository;
    protected AttachmentService $attachmentService;
    protected UserInCommunityRepository $userInCommunityRepository;
    protected NotificationRepository $notificationRepository;
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->attachmentInPostRepository = new AttachmentInPostRepository();
        $this->attachmentRepository = new AttachmentRepository();
        $this->attachmentService = new AttachmentService();
        $this->userInCommunityRepository = new UserInCommunityRepository();
        $this->notificationRepository = new NotificationRepository();
        $this->userRepository = new UserRepository();
    }

    public function submitPost(array $post, ?UploadedFile ...$files)
    {
        if (empty($post) || empty($post['id_user']))
            return false;

        $communityId = !empty($post['id_community']) ? (int) $post['id_community'] : null;

        $postData = [
            'id_user' => (int) $post['id_user'],
            'id_community' => $communityId,
            'title' => $post['title'],
            'description' => $post['description'] ?? '',
            'is_approved' => ($communityId === null),
            'is_deleted' => false,
            'posted_at' => date('Y-m-d H:i:s'),
        ];

        $postId = $this->postRepository->createPost($postData);
        if (!$postId) {
            log_message('error', '[PostService] Falha ao criar post no repositório.');
            return false;
        }

        if ($files !== null) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $attachmentId = $this->attachmentService->uploadFile($file);
                    if ($attachmentId) {
                        $this->attachmentInPostRepository->create($postId, $attachmentId);
                    }
                }
            }
        }

        try {
            if ($communityId) {
                $admins = $this->userInCommunityRepository->listAdministratorsByCommunity($communityId);
                foreach ($admins as $admin) {
                    $adminId = $admin->getIdUser();
                    if (!$this->notificationRepository->existsUnreadNotification($adminId, $communityId, 'pending_post')) {
                        $this->notificationRepository->notifyUser($adminId, 'pending_post', $communityId);
                    }
                }
            }

            $fullText = ($post['title'] ?? '') . ' ' . ($post['description'] ?? '');
            $namesMentioned = array_unique($this->getMentions($fullText));
            foreach ($namesMentioned as $name) {
                $user = $this->userRepository->getUserByName($name);
                if ($user) {
                    $mentionedUserId = $user->getId();
                    if (!$this->notificationRepository->existsUnreadNotification($mentionedUserId, $postId, 'mention_in_post')) {
                        $this->notificationRepository->notifyUser($mentionedUserId, 'mention_in_post', $postId);
                    }
                }
            }
        } catch (Throwable $e) {
            log_message('error', '[PostService] Falha não fatal na lógica de notificação: ' . $e->getMessage());
        }

        return $postId;
    }

    public function getPost(int $postId)
    {
        try {
            $post = $this->postRepository->findById($postId);
            if (!$post) {
                log_message('error', "[PostService::getPost] Post com ID {$postId} não encontrado.");
                return [null, []];
            }

            $attachmentsInPost = $this->attachmentInPostRepository->findAttachmentsInPost($postId);

            $attachmentDetails = [];
            foreach ($attachmentsInPost as $attachmentLink) {
                if (!$attachmentLink) {
                    log_message('warning', "[PostService::getPost] Encontrado um attachmentLink nulo para o post {$postId}.");
                    continue;
                }

                $attachment = $this->attachmentRepository->findById($attachmentLink->getIdAttachment());
                if ($attachment) {
                    $attachmentDetails[] = [
                        'id' => $attachment->getId(),
                        'type' => $attachment->getType()
                    ];
                } else {
                    log_message('warning', "[PostService::getPost] Anexo com ID {$attachmentLink->getIdAttachment()} não encontrado, mas link existe para o post {$postId}.");
                }
            }

            return [$post, $attachmentDetails];

        } catch (Throwable $e) {
            log_message('error', "[PostService::getPost] Exceção ao buscar post ID {$postId}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [null, []];
        }
    }

    private function getMentions(string $text): array
    {
        preg_match_all('/u\/([A-Za-z0-9_-]+)/', $text, $matches);
        return $matches[1] ?? [];
    }
}