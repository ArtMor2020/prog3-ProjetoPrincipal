<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\PostRepository;
use App\Services\PostService;

class PostController extends ResourceController
{
    protected $format = 'json';
    protected PostRepository $repository;
    protected PostService    $postService;

    public function __construct()
    {
        $this->repository  = new PostRepository();
        $this->postService = new PostService();
    }

    /**
     * GET /posts
     * GET /posts/community/{communityId}
     */
    public function index($communityId = null)
    {
        $posts = $communityId
            ? $this->repository->findAllByCommunity((int) $communityId)
            : $this->repository->findAll();

        return $this->respond($posts);
    }

    /**
     * GET /posts/{id}
     */
    public function show($id = null)
    {
        $post = $this->repository->findById((int) $id);

        return $post
            ? $this->respond($post)
            : $this->failNotFound('Post not found');
    }

    /**
     * GET /posts/title/{title}
     */
    public function getByTitle($title)
    {
        $posts = $this->repository->getPostsByTitle($title);
        return $this->respond($posts);
    }

    /**
     * GET /posts/popular?page={n}&posts-per-page={m}
     */
    public function getPopular()
    {
        $page         = $this->request->getGet('page') ?: null;
        $perPage      = $this->request->getGet('posts-per-page') ?: null;
        $posts        = $this->repository->getPopularPosts($page, $perPage);
        return $this->respond($posts);
    }

    /**
     * GET /posts/community/{id}/popular?period={day|week|month|year}&page={n}&posts-per-page={m}
     */
    public function getPopularInCommunity($communityId)
    {
        $valid      = ['day','week','month','year'];
        $period     = $this->request->getGet('period');
        $period     = in_array($period, $valid) ? $period : null;
        $page       = $this->request->getGet('page') ?: null;
        $perPage    = $this->request->getGet('posts-per-page') ?: null;

        $posts = $this->repository
            ->getPostsFromCommunityByPopularityInPeriod(
                (int)$communityId,
                $period,
                $page,
                $perPage
            );

        return $this->respond($posts);
    }

    /**
     * GET /posts/recommended/{userId}?page={n}&posts-per-page={m}
     */
    public function getRecommended($userId)
    {
        $page      = $this->request->getGet('page') ?: null;
        $perPage   = $this->request->getGet('posts-per-page') ?: null;
        $posts     = $this->repository->getRecommendedPostsForUser(
            (int)$userId,
            $page,
            $perPage
        );

        return $this->respond($posts);
    }

    /**
     * POST /posts/submit
     * Recebe form-data com campos do post + attachments[]
     */
    public function submit()
    {
        $post   = $this->request->getPost();
        $files  = $this->request->getFiles();
        $uploaded = [];

        if (! empty($files['attachments']))
        {
            $batch = is_array($files['attachments'])
                ? $files['attachments']
                : [$files['attachments']];

            foreach ($batch as $file)
            {
                if ($file->isValid() && ! $file->hasMoved())
                {
                    $uploaded[] = $file;
                }
            }
        }

        $postId = $this->postService->submitPost($post, ...$uploaded);

        if (! $postId) {
            return $this->failValidationError('Invalid post data.');
        }

        return $this->respondCreated(['id' => $postId]);
    }

    /**
     * POST /posts
     * (criação simples sem attachments)
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (
            empty($data['id_user']) ||
            empty($data['id_community']) ||
            empty($data['title']) ||
            empty($data['description'])
        ) {
            return $this->failValidationError(
                'Campos id_user, id_community, title e description são obrigatórios.'
            );
        }

        $id = $this->repository->createPost([
            'id_user'      => (int)$data['id_user'],
            'id_community' => (int)$data['id_community'],
            'title'        => $data['title'],
            'description'  => $data['description'],
        ]);

        if ($id === false) {
            return $this->fail('Não foi possível criar o post.', 400);
        }

        return $this->respondCreated(['id' => $id]);
    }

    /**
     * PUT /posts/{id}
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data['title']) && empty($data['description'])) {
            return $this->failValidationError(
                'É preciso ao menos title ou description para atualizar.'
            );
        }

        $success = $this->repository->update((int)$id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed', 400);
    }

    /**
     * DELETE /posts/{id}
     */
    public function delete($id = null)
    {
        $success = $this->repository->deletePost((int)$id);

        if (! $success) {
            $exists = (bool)$this->repository->findById((int)$id);
            return $exists
                ? $this->fail('Post already deleted or cannot be deleted', 400)
                : $this->failNotFound('Post not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }
}
