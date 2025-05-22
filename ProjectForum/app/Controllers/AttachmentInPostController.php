<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\AttachmentInPostRepository;

class AttachmentInPostController extends ResourceController
{
    /** @var AttachmentInPostRepository */
    protected $repo;

    public function __construct()
    {
        $this->repo = new AttachmentInPostRepository();
    }

    public function index()
    {
        return $this->respond($this->repo->findAll());
    }


    public function show($postId = null, $attachmentId = null)
    {
        $item = $this->repo->find((int) $postId, (int) $attachmentId);
        return $item
            ? $this->respond($item)
            : $this->failNotFound("Vínculo não encontrado");
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $ok = $this->repo->create((int) $data['id_post'], (int) $data['id_attachment']);

        return $ok
            ? $this->respondCreated(['status' => 'vinculado'])
            : $this->fail('Já existe ou dados inválidos', 400);
    }

    public function delete($postId = null, $attachmentId = null)
    {
        $ok = $this->repo->delete((int) $postId, (int) $attachmentId);
        return $ok
            ? $this->respondDeleted(['status' => 'desvinculado'])
            : $this->failNotFound('Associação não existe');
    }
}
