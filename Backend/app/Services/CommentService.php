<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\AttachmentInCommentRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use CodeIgniter\HTTP\Files\UploadedFile;

class CommentService
{
    protected CommentRepository $commentRepository;
    protected AttachmentService $attachmentService;
    protected AttachmentInCommentRepository $attachmentInCommentRepository;
    protected UserRepository $userRepository;
    protected NotificationRepository $notificationRepository;

    public function __construct()
    {
        $this->commentRepository = new CommentRepository();
        $this->attachmentService = new AttachmentService();
        $this->attachmentInCommentRepository = new AttachmentInCommentRepository();
        $this->userRepository = new UserRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    function submitComment(array $comment, ?UploadedFile ...$files)
    {
        if (empty($comment))
            return false;

        $attachmentIds = [];

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $attachmentIds[] = $this->attachmentService->uploadFile($file);
            }
        }

        $commentId = $this->commentRepository->create($comment);

        foreach ($attachmentIds as $attachmentId) {
            if ($attachmentId) {
                $this->attachmentInCommentRepository->create($commentId, $attachmentId);
            }
        }

        $namesMentioned = array_unique($this->getMentions($comment['content']));

        foreach ($namesMentioned as $name) {
            $user = $this->userRepository->getUserByName($name);

            if (
                $user &&
                !$this->notificationRepository->existsUnreadNotification(
                    $user->getId(),
                    $commentId,
                    'mention_in_comment'
                )
            ) {
                $this->notificationRepository->notifyUser(
                    $user->getId(),
                    'mention_in_comment',
                    $commentId
                );
            }
        }

        return $commentId;
    }

    private function getMentions(string $text): array
    {
        preg_match_all('/u\/([A-Za-z0-9_-]+)/', $text, $matches);
        return $matches[1] ?? [];
    }
}