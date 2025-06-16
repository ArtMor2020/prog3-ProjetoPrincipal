<?php

namespace App\Services;

use App\Database\Migrations\Attachment;
use App\Database\Migrations\AttachmentInPost;
use App\Database\Migrations\RatingInPost;
use App\Repositories\PostRepository;
use App\Entities\PostEntity;
use App\Repositories\AttachmentInPostRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RatingInPostRepository;
use App\Repositories\UserInCommunityRepository;
use App\Repositories\UserRepository;
use CodeIgniter\HTTP\Files\UploadedFile;

class PostService
{
    protected PostRepository $postRepository;
    protected RatingInPostRepository $ratingInPostRepository;
    protected AttachmentRepository $attachmentRepository;
    protected AttachmentInPostRepository $attachmentInPostRepository;
    protected AttachmentService $attachmentService;
    protected UserInCommunityRepository $userInCommunityRepository;
    protected NotificationRepository $notificationRepository;
    protected UserRepository $userRepository;

    public function __construct(){
        $this->postRepository = new PostRepository();
        $this->ratingInPostRepository = new RatingInPostRepository();
        $this->attachmentRepository = new AttachmentRepository();
        $this->attachmentInPostRepository = new AttachmentInPostRepository();
        $this->attachmentService = new AttachmentService();
        $this->userInCommunityRepository = new UserInCommunityRepository();
    }

    public function submitPost(array $post, ?UploadedFile ...$files){

        if(empty($post)) return false;

        // Garante posted_at correto
        $post['posted_at'] = date('Y-m-d H:i:s');

        // add post to db
        $postId = $this->postRepository->createPost($post);
        $attachmentIds = [];

        // checks if there are any attachments
        if($files !== null)
        {
            // saves file to disk and add attachment to db
            foreach($files as $file){
                $attachmentIds[] = $this->attachmentService->uploadFile($file);
            }

            // add relation of post and attachments to db
            foreach($attachmentIds as $attachmentId){
                $this->attachmentInPostRepository->create($postId, $attachmentId);
            }
        }

        // makes notification to community administrators
        $admins = $this->userInCommunityRepository->listAdministratorsByCommunity($post['id_community']);

        foreach ($admins as $admin)
        {
            if( !$this->notificationRepository->existsUnreadNotification(
                $admin['id_user'], 
                $post['id_community'], 
                'pending_post'))
            {
                $this->notificationRepository->notifyUser(
                    $admin['id_user'], 
                    'pending_post', 
                    $post['id_community']);
            }
        }

        // makes notifications for mentions
        $namesMentioned = array_unique(array_merge(
            $this->getMentions($post['title']),
            $this->getMentions($post['description'])
        ));

        foreach ($namesMentioned as $name) 
        {
            $user = $this->userRepository->getUserByName($name);

            if ($user && 
                !$this->notificationRepository->existsUnreadNotification(
                    $user['id'], $postId, 'mention_in_post')
                )
            {
                $this->notificationRepository->notifyUser(
                    $user['id'], 
                    'mention_in_post', 
                    $postId
                );
            }
        }

        return $postId;
    }

    // call as [$post, $files] = $this->postServiçe->getPost($postId);
    public function getPost(int $postId)
    {
        // get post
        $post = $this->postRepository->getPost($postId);

        // get attachments
        $attachments = $this->attachmentInPostRepository->findAttachmentsInPost($postId);
        $files = [];

        foreach ($attachments as $attachment)
        {
            $files[] = $this->attachmentService->getFile($attachment['id_attachment']);
        }

        return [$post, $files];
    }

    private function getMentions(string $text): array
    {
        preg_match_all('/u\/([A-Za-z0-9_-]+)/', $text, $matches);
        return $matches[1] ?? [];
    }
}