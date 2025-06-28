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
        return $att ? $this->respond($att) : $this->failNotFound('Attachment não encontrado');
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
            if (!is_numeric($id)) {
                return $this->failValidationError('ID de anexo inválido.');
            }

            $attachment = $this->repo->findById((int)$id);

            if (!$attachment || $attachment->getIsDeleted()) {
                return $this->failNotFound('Anexo não encontrado ou foi deletado.');
            }

            $filePath = $attachment->getPath();

            if (!file_exists($filePath)) {
                log_message('error', "[AttachmentController] Arquivo não encontrado no disco: {$filePath}");
                return $this->failNotFound('Arquivo não existe no servidor.');
            }

            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                throw new \RuntimeException('Não foi possível ler o arquivo.');
            }

            $mimeType = mime_content_type($filePath);

            $this->response->setContentType($mimeType);

            $this->response->setBody($fileContent);

            return $this->response;

        } catch (Throwable $e) {
            log_message('error', "[AttachmentController::serve] Exceção: " . $e->getMessage());
            return $this->failServerError('Ocorreu um erro ao tentar servir o anexo.');
        }
    }
}
