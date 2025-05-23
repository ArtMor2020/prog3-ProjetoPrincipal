<?php
namespace App\Repositories;

use CodeIgniter\Database\ConnectionInterface;
use Config\Database;

class AttachmentInCommentRepository
{
    private ConnectionInterface $db;
    private string $table = 'attachment_in_comment';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findAll(): array
    {
        return $this->db
            ->table($this->table)
            ->get()
            ->getResultArray();
    }

    public function find(int $commentId, int $attachmentId): ?array
    {
        return $this->db
            ->table($this->table)
            ->where('id_comment', $commentId)
            ->where('id_attachment', $attachmentId)
            ->get()
            ->getFirstRow('array');
    }

    public function create(int $commentId, int $attachmentId): bool
    {
        $builder = $this->db->table($this->table);

        if (
            $builder->where('id_comment', $commentId)
                ->where('id_attachment', $attachmentId)
                ->countAllResults() > 0
        ) {
            return false;
        }

        return (bool) $builder->insert([
            'id_comment' => $commentId,
            'id_attachment' => $attachmentId,
        ]);
    }

    public function delete(int $commentId, int $attachmentId): bool
    {
        return (bool) $this->db
            ->table($this->table)
            ->where('id_comment', $commentId)
            ->where('id_attachment', $attachmentId)
            ->delete();
    }
}
