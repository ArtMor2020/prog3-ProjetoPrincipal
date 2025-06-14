<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\RatingInPostRepository;

class RatingInPostController extends ResourceController
{
    protected $format = 'json';
    protected RatingInPostRepository $repository;

    public function __construct()
    {
        $this->repository = new RatingInPostRepository();
    }

    /**
     * POST /ratings-in-posts/{postId}/votes
     */
    public function toggle($postId)
    {
        $payload  = $this->request->getJSON(true);
        $userId   = $payload['id_user'] ?? null;
        $isUpvote = isset($payload['is_upvote']) ? (bool) $payload['is_upvote'] : null;

        if (!$userId || $isUpvote === null) {
            return $this->failValidationError('Campos id_user e is_upvote são obrigatórios');
        }

        $ok = $this->repository->toggleVote((int) $postId, (int) $userId, $isUpvote);

        if (! $ok) {
            return $this->fail('Não foi possível processar voto', 400);
        }

        return $this->respond(['status' => 'ok']);
    }

    /**
     * GET /ratings-in-posts/{postId}/votes
     * Lista todos os votos (para saber se o usuário já votou)
     */
    public function list($postId)
    {
        $votes = $this->repository->getVotesForPost((int) $postId);
        return $this->respond($votes);
    }

    /**
     * GET /ratings-in-posts/{postId}/score
     */
    public function score($postId = null)
    {
        if (empty($postId) || !is_numeric($postId)) {
            return $this->failValidationError('Post ID inválido');
        }

        $score = $this->repository->getScore((int) $postId);

        return $this->respond([
            'post_id' => (int) $postId,
            'score'   => $score,
        ]);
    }

    /**
     * DELETE /ratings-in-posts/{postId}/vote/delete
     * Body JSON: { "id_user": 123 }
     */
    public function remove($postId)
    {
        $data = $this->request->getJSON(true);
        if (empty($data['id_user'])) {
            return $this->failValidationError('Campo id_user é obrigatório');
        }

        $success = $this->repository->removeVote((int) $postId, (int) $data['id_user']);

        if (! $success) {
            return $this->failNotFound('Vote not found or already removed.');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }
}
