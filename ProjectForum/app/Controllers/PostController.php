<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\PostRepository;

class PostController extends ResourceController
{
    protected $format = 'json';
    protected PostRepository $repository;

    public function __construct()
    {
        $this->repository = new PostRepository();
    }

    public function index($communityId = null)
    {
        $posts = $communityId
            ? $this->repository->findAllByCommunity((int) $communityId)
            : $this->repository->findAll();

        return $this->respond($posts);
    }

    public function show($id = null)
    {
        $post = $this->repository->findById((int) $id);

        return $post
            ? $this->respond($post)
            : $this->failNotFound('Post not found');
    }

    public function create()
    {

        $data = $this->request->getJSON(true);

        if (
            empty($data['id_user']) || empty($data['id_community'])
            || empty($data['title']) || empty($data['description'])
        ) {
            return $this->failValidationError(
                'Campos id_user, id_community, title e description são obrigatórios.'
            );
        }

        $id = $this->repository->createPost([
            'id_user' => (int) $data['id_user'],
            'id_community' => (int) $data['id_community'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        if ($id === false) {
            return $this->fail('Não foi possível criar o post.', 400);
        }

        return $this->respondCreated(['id' => $id]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data['title']) && empty($data['description'])) {
            return $this->failValidationError('É preciso ao menos title ou description para atualizar.');
        }

        $success = $this->repository->update((int) $id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed', 400);
    }

    public function delete($id = null)
    {
        $success = $this->repository->deletePost((int) $id);

        if (!$success) {
            $exists = (bool) $this->repository->findById((int) $id);

            return $exists
                ? $this->fail('Post already deleted or cannot be deleted', 400)
                : $this->failNotFound('Post not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }
}
