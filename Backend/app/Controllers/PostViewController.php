<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\PostViewRepository;

class PostViewController extends ResourceController
{
    protected $format = 'json';
    protected PostViewRepository $repository;

    public function __construct()
    {
        $this->repository = new PostViewRepository();
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (empty($data['post_id']) || empty($data['user_id'])) {
            return $this->failValidationError('post_id e user_id são obrigatórios');
        }

        $ok = $this->repository->addView((int) $data['post_id'], (int) $data['user_id']);
        if (!$ok) {
            return $this->fail('Não foi possível registrar a visualização', 500);
        }

        return $this->respondCreated([
            'post_id' => (int) $data['post_id'],
            'user_id' => (int) $data['user_id'],
            'viewed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function count($postId = null)
    {
        if (!$postId || !is_numeric($postId)) {
            return $this->failValidationError('Post ID inválido');
        }

        $count = $this->repository->getViewsCount((int) $postId);
        return $this->respond(['post_id' => (int) $postId, 'views' => $count]);
    }

    public function list($postId = null)
    {
        if (!$postId || !is_numeric($postId)) {
            return $this->failValidationError('Post ID inválido');
        }

        $views = $this->repository->listViewers((int) $postId);
        return $this->respond($views);
    }

}