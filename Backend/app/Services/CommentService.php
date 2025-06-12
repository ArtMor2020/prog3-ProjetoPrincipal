<?php

namespace App\Services;

use App\Database\Migrations\Attachment;
use App\Database\Migrations\AttachmentInComment;
use App\Entities\CommentEntity;
use App\Repositories\CommentRepository;
use App\Repositories\AttachmentInCommentRepository;
use CodeIgniter\HTTP\Files\UploadedFile;

class CommentService
{
    protected CommentRepository $commentRepository;
    protected AttachmentService $attachmentService;
    protected AttachmentInCommentRepository $attachmentInCommentRepository;
    
    public function __construct() {
        $this->commentRepository = new CommentRepository();
        $this->attachmentService = new AttachmentService();
        $this->attachmentInCommentRepository = new AttachmentInCommentRepository();
    }

    function submitComment(array $comment, ?UploadedFile ...$files){
        if(empty($comment)) return false;

        $attachmentIds = [];

        foreach($files as $file) {
            $attachmentIds[] = $this->attachmentService->uploadFile($file);
        }

        $commentId = $this->commentRepository->create($comment);

        foreach($attachmentIds as $attachmentId){
            $this->attachmentInCommentRepository->create($commentId, $attachmentId);
        }

        return $commentId;
    }
}