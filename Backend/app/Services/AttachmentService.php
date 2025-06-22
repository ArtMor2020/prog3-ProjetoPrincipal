<?php

namespace App\Services;

use App\Repositories\AttachmentRepository;
use CodeIgniter\HTTP\Files\UploadedFile;
use Throwable;

class AttachmentService
{
    protected AttachmentRepository $attachmentRepository;

    public function __construct()
    {
        $this->attachmentRepository = new AttachmentRepository();
    }

    public function uploadFile(UploadedFile $file): ?int
    {

        if (!$file->isValid()) {
            log_message('error', '[AttachmentService] Tentativa de upload de arquivo inválido: ' . $file->getErrorString());
            return null;
        }

        $extension = $file->getExtension();
        $newName = $file->getRandomName();

        $uploadPath = WRITEPATH . 'uploads/attachments/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        try {
            $file->move($uploadPath, $newName);
        } catch (Throwable $e) {
            log_message('error', '[AttachmentService] Falha ao mover o arquivo: ' . $e->getMessage());
            return null;
        }

        $fileData = [
            'type' => $this->getExtensionType($extension),
            'path' => $uploadPath . $newName
        ];

        // ----------------------------------------

        return $this->attachmentRepository->create($fileData) ?: null;
    }

    public function getFile(int $attachmentId): array
    {
        $attachment = $this->attachmentRepository->findById($attachmentId);
        if (!$attachment || $attachment->getIsDeleted()) {
            throw new \RuntimeException('Arquivo não encontrado ou foi deletado.');
        }
        $filePath = $attachment->getPath();
        if (!file_exists($filePath)) {
            throw new \RuntimeException('Arquivo não existe no servidor.');
        }
        return [
            'name' => basename($filePath),
            'type' => $attachment->getType(),
            'size' => filesize($filePath),
            'content' => file_get_contents($filePath)
        ];
    }

    public function getExtensionType(string $EXT): string
    {
        $EXT = strtolower($EXT);
        $IMAGE = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'];
        $VIDEO = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
        $DOCUMENT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'odt'];
        $ZIP = ['zip', 'rar', '7z', 'tar', 'gz'];

        return match (true) {
            in_array($EXT, $IMAGE) => 'IMAGE',
            in_array($EXT, $VIDEO) => 'VIDEO',
            in_array($EXT, $DOCUMENT) => 'DOCUMENT',
            in_array($EXT, $ZIP) => 'ZIP',
            default => 'OTHER'
        };
    }
}