<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\PostRepository;
use App\Services\PostService;

class PostController extends ResourceController
{
    protected $format = 'json';
    protected PostRepository $repository;
    protected PostService $postService;

    public function __construct()
    {
        $this->repository = new PostRepository();
        $this->postService = new PostService();
    }

    public function index($communityId = null)
    {
        $viewerId = $this->request->getGet('viewerId');

        $posts = $communityId
            ? $this->repository->findAllByCommunity((int) $communityId, (int) $viewerId)
            : $this->repository->findAll((int) $viewerId);

        return $this->respond($posts);
    }

    public function show($id = null)
    {
        [$post, $attachments] = $this->postService->getPost((int) $id);

        if (!$post) {
            return $this->failNotFound('Post não encontrado ou falha ao processar dados.');
        }

        return $this->respond([$post, $attachments]);
    }

    public function getByTitle($title)
    {
        $posts = $this->repository->getPostsByTitle($title);
        return $this->respond($posts);
    }

    public function getPopular()
    {
        $page = $this->request->getGet('page') ?: null;
        $perPage = $this->request->getGet('posts-per-page') ?: null;
        $posts = $this->repository->getPopularPosts($page, $perPage);
        return $this->respond($posts);
    }

    public function getPopularInCommunity($communityId)
    {
        $valid = ['day', 'week', 'month', 'year'];
        $period = $this->request->getGet('period');
        $period = in_array($period, $valid) ? $period : null;
        $page = $this->request->getGet('page') ?: null;
        $perPage = $this->request->getGet('posts-per-page') ?: null;

        $posts = $this->repository
            ->getPostsFromCommunityByPopularityInPeriod(
                (int) $communityId,
                $period,
                $page,
                $perPage
            );

        return $this->respond($posts);
    }

    public function getRecommended($userId)
    {
        $page = $this->request->getGet('page') ?: null;
        $perPage = $this->request->getGet('posts-per-page') ?: null;
        $posts = $this->repository->getRecommendedPostsForUser(
            (int) $userId,
            $page,
            $perPage
        );

        return $this->respond($posts);
    }

    public function submit()
    {
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $uploaded = [];

        if (!empty($files['attachments'])) {
            $batch = $files['attachments'];
            foreach ($batch as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $uploaded[] = $file;
                }
            }
        }

        $postId = $this->postService->submitPost($post, ...$uploaded);

        if (!$postId) {
            return $this->failValidationError('Dados de post inválidos ou falha ao processar.');
        }

        return $this->respondCreated(['id' => $postId]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validação agora só exige id_user e title
        if (empty($data['id_user']) || empty($data['title'])) {
            return $this->failValidationError(
                'Campos id_user e title são obrigatórios.'
            );
        }

        $id = $this->repository->createPost([
            'id_user' => (int) $data['id_user'],
            'id_community' => isset($data['id_community']) && !empty($data['id_community']) ? (int) $data['id_community'] : null,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
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
        return $success ? $this->respond(['status' => 'updated']) : $this->fail('Update failed', 400);
    }

    public function delete($id = null)
    {
        $success = $this->repository->deletePost((int) $id);
        if (!$success) {
            $exists = (bool) $this->repository->findById((int) $id);
            return $exists ? $this->fail('Post já deletado ou não pode ser deletado', 400) : $this->failNotFound('Post não encontrado');
        }
        return $this->respondDeleted(['status' => 'deleted']);
    }
}
