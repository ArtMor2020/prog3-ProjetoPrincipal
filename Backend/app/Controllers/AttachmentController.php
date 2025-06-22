<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\AttachmentRepository;
use App\Services\AttachmentService;

class AttachmentController extends ResourceController
{
    protected $repo;
    protected AttachmentService $service;

    public function __construct()
    {
        $this->repo = new AttachmentRepository();
        $this->service = new AttachmentService();

    }

    public function index()
    {
        return $this->respond($this->repo->findAll());
    }

    public function show($id = null)
    {
        $att = $this->repo->findById((int) $id);
        return $att
            ? $this->respond($att)
            : $this->failNotFound('Attachment não encontrado');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $id = $this->repo->create($data);

        return $id
            ? $this->respondCreated(['id' => $id])
            : $this->fail('Falha ao criar attachment', 400);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $ok = $this->repo->update((int) $id, $data);

        return $ok
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Falha ao atualizar', 400);
    }

    public function delete($id = null)
    {
        $ok = $this->repo->delete((int) $id);

        if (!$ok) {
            $exists = (bool) $this->repo->findById((int) $id);
            return $exists
                ? $this->fail('Attachment já deletado', 400)
                : $this->failNotFound('Attachment não existe');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }

    public function restore($id = null)
    {
        $ok = $this->repo->restore((int) $id);

        return $ok
            ? $this->respond(['status' => 'restored'])
            : $this->failNotFound('Não foi possível restaurar');
    }

    public function serve($id = null)
    {
        try {
            $fileData = $this->service->getFile((int) $id);

            $this->response->setContentType($fileData['type']);
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $fileData['name'] . '"');

            return $this->response->setBody($fileData['content']);

        } catch (\RuntimeException $e) {
            return $this->failNotFound($e->getMessage());
        }
    }
}
