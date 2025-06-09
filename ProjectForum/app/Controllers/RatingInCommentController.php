<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\RatingInCommentRepository;

class RatingInCommentController extends ResourceController
{
    protected RatingInCommentRepository $repo;

    public function __construct()
    {
        $this->repo = new RatingInCommentRepository();
    }

    public function toggle($commentId = null)
    {
        $payload = $this->request->getJSON(true);
        if (empty($payload['id_user']) || !isset($payload['is_upvote'])) {
            return $this->failValidationError('Campos id_user e is_upvote são obrigatórios');
        }

        $ok = $this->repo->toggleVote(
            (int) $commentId,
            (int) $payload['id_user'],
            (bool) $payload['is_upvote']
        );

        return $ok
            ? $this->respond(['status' => 'voto toggled'])
            : $this->fail('Falha ao processar voto', 500);
    }

    public function score($commentId = null)
    {
        $score = $this->repo->getScore((int) $commentId);
        return $this->respond(['score' => $score]);
    }

    public function listVotes($commentId = null)
    {
        $votes = $this->repo->getVotes((int) $commentId);
        return $this->respond($votes);
    }

    public function remove($commentId)
    {
        $data = $this->request->getJSON(true);

        if ( empty($data['id_user']) ) return $this->failValidationError('Campos id_user é obrigatório');

        $commentId = (int) $commentId;
        $userId = (int) $data['id_user'];

        $success = $this->repo->removeVote($commentId, $userId);

        if (!$success) {
            return $this->failNotFound('Vote not found or already removed.');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }
}
