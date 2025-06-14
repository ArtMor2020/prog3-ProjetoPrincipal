<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\CommentRepository;
use App\Services\CommentService;

class CommentController extends ResourceController
{
    protected $format = 'json';
    protected CommentRepository $repository;
    protected CommentService $commentService;

    public function __construct()
    {
        $this->repository     = new CommentRepository();
        $this->commentService = new CommentService();
    }

    /**
     * GET /comments            -> index()
     * GET /comments/post/{id}  -> index($postId)
     */
    public function index($postId = null)
    {
        $comments = $postId
            ? $this->repository->findAllByPost((int)$postId)
            : $this->repository->findAll();

        return $this->respond($comments);
    }

    /**
     * GET /comments/{id}
     */
    public function show($id = null)
    {
        $comment = $this->repository->findById((int)$id);

        return $comment
            ? $this->respond($comment)
            : $this->failNotFound('Comment not found');
    }

    /**
     * GET /comments/comment/{id}
     */
    public function byParent($commentId = null)
    {
        if (empty($commentId) || !is_numeric($commentId)) {
            return $this->failValidationError('Parent comment ID inválido.');
        }

        $replies = $this->repository->findByParentComment((int)$commentId);
        $data    = array_map(fn($entity) => $entity->toArray(), $replies);

        return $this->respond($data);
    }

    /**
     * POST /comments
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_user']) || empty($data['content'])) {
            return $this->failValidationError('Campos id_user e content são obrigatórios');
        }

        $id = $this->repository->create($data);

        return $this->respondCreated(['id' => $id]);
    }

    /**
     * POST /comments/submit
     * para upload de anexos junto com o comentário
     */
    public function submit()
    {
        $comment = $this->request->getPost();
        $files   = $this->request->getFiles();
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

    /**
     * POST /comments/{id}/reply
     */
    public function reply($commentId = null)
    {
        if (empty($commentId) || !is_numeric($commentId)) {
            return $this->failValidationError('Parent comment ID inválido.');
        }

        $data = $this->request->getJSON(true);
        if (empty($data['id_user']) || empty($data['content'])) {
            return $this->failValidationError('É necessário id_user e content.');
        }

        $newId = $this->repository->createReply((int)$commentId, [
            'id_user' => (int)$data['id_user'],
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

    /**
     * PUT /comments/{id}
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data) || !isset($data['content'])) {
            return $this->failValidationError('Campo content é obrigatório.');
        }

        $success = $this->repository->update((int)$id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed');
    }

    /**
     * DELETE /comments/{id}
     */
    public function delete($id = null)
    {
        $success = $this->repository->deleteComment((int)$id);

        if (!$success) {
            $exists = (bool)$this->repository->findById((int)$id);
            return $exists
                ? $this->fail('Comment already deleted or cannot be deleted', 400)
                : $this->failNotFound('Comment not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }
}
