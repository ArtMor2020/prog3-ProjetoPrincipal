<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\AttachmentInCommentRepository;

class AttachmentInCommentController extends ResourceController
{
    protected AttachmentInCommentRepository $repo;

    public function __construct()
    {
        $this->repo = new AttachmentInCommentRepository();
    }

    public function index()
    {
        return $this->respond($this->repo->findAll());
    }

    public function show($commentId = null, $attachmentId = null)
    {
        $item = $this->repo->find((int) $commentId, (int) $attachmentId);
        return $item
            ? $this->respond($item)
            : $this->failNotFound('Vínculo não encontrado');
    }

    public function create()
    {
        $payload = $this->request->getJSON(true);
        if (empty($payload['id_comment']) || empty($payload['id_attachment'])) {
            return $this->failValidationError('Campos id_comment e id_attachment são obrigatórios');
        }

        $ok = $this->repo->create(
            (int) $payload['id_comment'],
            (int) $payload['id_attachment']
        );

        if (!$ok) {
            return $this->fail('Já existe ou dados inválidos', 400);
        }

        return $this->respondCreated(['status' => 'anexo vinculado']);
    }

    public function delete($commentId = null, $attachmentId = null)
    {
        $ok = $this->repo->delete((int) $commentId, (int) $attachmentId);
        return $ok
            ? $this->respondDeleted(['status' => 'vínculo removido'])
            : $this->failNotFound('Associação não existe');
    }
}
