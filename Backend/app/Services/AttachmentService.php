<?php

namespace App\Services;

use App\Database\Migrations\Attachment;
use App\Repositories\AttachmentRepository;
use CodeIgniter\HTTP\Files\UploadedFile;

class AttachmentService
{
    protected AttachmentRepository $attachmentRepository;

    public function __construct(){
        $this->attachmentRepository = new AttachmentRepository();
    }

    public function uploadFile(UploadedFile $file): ?int{

        if (!$file->isValid()) return null;

        // gives unique name to file
        $newName = uniqid('', true) . '.' . $file->getExtension();
        $uploadPath = WRITEPATH . 'uploads/attachments/';
 
        // makes directory if doesnt exists
        if (!is_dir($uploadPath)){
            mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $newName);

        $fileData = [
            'type' => $this->getExtensionType($file->getExtension()),
            'path' => $uploadPath . $newName
        ];

        return $this->attachmentRepository->create($fileData) ?: null;
    }

    public function getFile(int $attachmentId): array
    {
        // Fetch attachment by ID
        $attachment = $this->attachmentRepository->findById($attachmentId);

        if (!$attachment || $attachment->getIsDeleted()) {
            throw new \RuntimeException('File not found or has been deleted.');
        }

        $filePath = $attachment->getPath();

        if (!file_exists($filePath)) {
            throw new \RuntimeException('File does not exist on server.');
        }

        return [
            'name'     => basename($filePath),
            'type'     => $attachment->getType(),
            'size'     => filesize($filePath),
            'content'  => file_get_contents($filePath)
        ];
    }

    public function getExtensionType(string $EXT): string
    {
        $EXT = strtolower($EXT);

        $IMAGE    = ['jpg', 'jpeg', 'png', 'webp', 'bmp'];
        $VIDEO    = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
        $GIF      = ['gif'];
        $DOCUMENT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'odt'];
        $ZIP      = ['zip', 'rar', '7z', 'tar', 'gz'];

        return match (true) {
            in_array($EXT, $IMAGE)    => 'IMAGE',
            in_array($EXT, $VIDEO)    => 'VIDEO',
            in_array($EXT, $GIF)      => 'GIF',
            in_array($EXT, $DOCUMENT) => 'DOCUMENT',
            in_array($EXT, $ZIP)      => 'ZIP',
            default                   => 'OTHER'
        };
    }
}