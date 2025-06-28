<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommentRepository;
use App\Services\CommentService;
use App\Services\NotificationService;

class CommentController extends ResourceController
{
    protected $format = 'json';
    protected CommentRepository $repository;
    protected CommentService $commentService;

    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->repository = new CommentRepository();
        $this->commentService = new CommentService();
    }

    public function index($postId = null)
    {
        $viewerId = $this->request->getGet('viewerId');

        $comments = $postId
            ? $this->repository->findAllByPost((int) $postId, (int) $viewerId)
            : $this->repository->findAll((int) $viewerId);

        return $this->respond($comments);
    }

    public function show($id = null)
    {
        $comment = $this->repository->findById((int) $id);

        return $comment
            ? $this->respond($comment)
            : $this->failNotFound('Comment not found');
    }

    public function byParent($commentId = null)
    {
        if (empty($commentId) || !is_numeric($commentId)) {
            return $this->failValidationError('Parent comment ID inválido.');
        }

        $replies = $this->repository->findByParentComment((int) $commentId);
        $data = array_map(fn($entity) => $entity->toArray(), $replies);

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_user']) || empty($data['content'])) {
            return $this->failValidationError('Campos id_user e content são obrigatórios');
        }

        $id = $this->repository->create($data);

        return $this->respondCreated(['id' => $id]);
    }

    public function submit()
    {
        $comment = $this->request->getJSON(true);
        // -------------------------

        $files = $this->request->getFiles();
        $uploadedFiles = [];

        if (!empty($files['attachments'])) {
            $attachments = is_array($files['attachments'])
                ? $files['attachments']
                : [$files['attachments']];

            foreach ($attachments as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $uploadedFiles[] = $file;
                }
            }
        }

        $commentId = $this->commentService->submitComment(
            $comment,
            ...$uploadedFiles
        );

        if (!$commentId) {
            return $this->failValidationError('Invalid comment data.');
        }

        return $this->respondCreated(['id' => $commentId]);
    }

    public function reply($commentId = null)
    {
        if (empty($commentId) || !is_numeric($commentId)) {
            return $this->failValidationError('Parent comment ID inválido.');
        }

        $data = $this->request->getJSON(true);
        if (empty($data['id_user']) || empty($data['content'])) {
            return $this->failValidationError('É necessário id_user e content.');
        }

        $newId = $this->repository->createReply((int) $commentId, [
            'id_user' => (int) $data['id_user'],
            'content' => $data['content'],
        ]);

        if ($newId === false) {
            return $this->failNotFound(
                'Comentário pai não encontrado ou erro ao criar resposta.',
                400
            );
        }

        return $this->respondCreated(['id' => $newId]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data) || !isset($data['content'])) {
            return $this->failValidationError('Campo content é obrigatório.');
        }

        $success = $this->repository->update((int) $id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed');
    }

    public function delete($id = null)
    {
        $success = $this->repository->deleteComment((int) $id);

        if (!$success) {
            $exists = (bool) $this->repository->findById((int) $id);
            return $exists
                ? $this->fail('Comment already deleted or cannot be deleted', 400)
                : $this->failNotFound('Comment not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }

    public function report($commentId = null)
    {
        if (!is_numeric($commentId)) {
            return $this->failValidationError('ID de comentário inválido.');
        }

        $comment = $this->repository->findById((int)$commentId);
        if (!$comment) {
            return $this->failNotFound('Comentário não encontrado.');
        }

        $notificationService = new NotificationService();
        $success = $notificationService->notifyAboutReportedComment($comment);

        if (!$success) {
            return $this->fail('Não foi possível registrar o report.', 500);
        }

        return $this->respond(['status' => 'reported'], 200);
    }
}
