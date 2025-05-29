<?php

namespace App\Services;

use App\Database\Migrations\Attachment;
use App\Database\Migrations\AttachmentInPost;
use App\Database\Migrations\RatingInPost;
use App\Repositories\PostRepository;
use App\Entities\PostEntity;
use App\Repositories\AttachmentInPostRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\RatingInPostRepository;
use CodeIgniter\HTTP\Files\UploadedFile;

class PostService
{
    protected PostRepository $postRepository;
    protected RatingInPostRepository $ratingInPostRepository;
    protected AttachmentRepository $attachmentRepository;
    protected AttachmentInPostRepository $attachmentInPostRepository;
    protected AttachmentService $attachmentService;

    public function __construct(){
        $this->postRepository = new PostRepository();
        $this->ratingInPostRepository = new RatingInPostRepository();
        $this->attachmentRepository = new AttachmentRepository();
        $this->attachmentInPostRepository = new AttachmentInPostRepository();
        $this->attachmentService = new AttachmentService();
    }

    public function submitPost(array $post, ?UploadedFile ...$files){

        if(empty($post)) return false;

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

        return $postId;
    }
}