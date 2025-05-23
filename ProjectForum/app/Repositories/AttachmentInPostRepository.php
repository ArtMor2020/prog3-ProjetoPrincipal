<?php
namespace App\Repositories;

use CodeIgniter\Database\ConnectionInterface;
use Config\Database;

class AttachmentInPostRepository
{
    private ConnectionInterface $db;
    private string $table = 'attachment_in_post';

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

    public function find(int $postId, int $attachmentId): ?array
    {
        return $this->db
            ->table($this->table)
            ->where('id_post', $postId)
            ->where('id_attachment', $attachmentId)
            ->get()
            ->getFirstRow('array');
    }

    public function create(int $postId, int $attachmentId): bool
    {
        $builder = $this->db->table($this->table);

        if (
            $builder->where('id_post', $postId)
                ->where('id_attachment', $attachmentId)
                ->countAllResults() > 0
        ) {
            return false;
        }

        return (bool) $builder->insert([
            'id_post' => $postId,
            'id_attachment' => $attachmentId,
        ]);
    }

    public function delete(int $postId, int $attachmentId): bool
    {
        return (bool) $this->db
            ->table($this->table)
            ->where('id_post', $postId)
            ->where('id_attachment', $attachmentId)
            ->delete();
    }
}
