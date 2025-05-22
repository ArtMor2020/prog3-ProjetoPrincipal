<?php
namespace App\Repositories;

use CodeIgniter\Database\ConnectionInterface;
use Config\Database;

class RatingInCommentRepository
{
    private ConnectionInterface $db;
    private string $table = 'rating_in_comment';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function toggleVote(int $commentId, int $userId, bool $isUpvote): bool
    {
        $builder = $this->db->table($this->table);

        $existing = $builder
            ->where('id_comment', $commentId)
            ->where('id_user', $userId)
            ->get()
            ->getFirstRow('array');

        if (!$existing) {
            return (bool) $builder->insert([
                'id_comment' => $commentId,
                'id_user' => $userId,
                'is_upvote' => $isUpvote,
            ]);
        }

        if ((bool) $existing['is_upvote'] === $isUpvote) {
            return (bool) $builder
                ->where('id_comment', $commentId)
                ->where('id_user', $userId)
                ->delete();
        }

        return (bool) $builder
            ->where('id_comment', $commentId)
            ->where('id_user', $userId)
            ->update(['is_upvote' => $isUpvote]);
    }

    public function getScore(int $commentId): int
    {
        $up = $this->db->table($this->table)
            ->where(['id_comment' => $commentId, 'is_upvote' => 1])
            ->countAllResults();
        $down = $this->db->table($this->table)
            ->where(['id_comment' => $commentId, 'is_upvote' => 0])
            ->countAllResults();

        return $up - $down;
    }

    public function getVotes(int $commentId): array
    {
        return $this->db
            ->table($this->table)
            ->where('id_comment', $commentId)
            ->get()
            ->getResultArray();
    }
}
