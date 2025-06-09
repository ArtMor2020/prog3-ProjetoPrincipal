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

    public function getByTitle($title)
    {
        $posts = $this->repository->getPostsByTitle($title);

        return $this->respond($posts);
    }

    public function getPopular()
    {
        $page = $this->request->getGet('page') ?: null;
        $postsPerPage = $this->request->getGet('posts-per-page') ?: null;

        log_message('error','0');

        $posts = $this->repository->getPopularPosts($page, $postsPerPage);
        log_message('error','3');
        return $this->respond($posts);
    }

    public function getPopularInCommunity($communityId)
    {
        $validPeriods = ['day', 'week', 'month', 'year'];
        $period = $this->request->getGet('period');
        $period = in_array($period, $validPeriods) ? $period : null;

        $page = $this->request->getGet('page') ?: null;
        $postsPerPage = $this->request->getGet('posts-per-page') ?: null;

        $posts = $this->repository->getPostsFromCommunityByPopularityInPeriod($communityId, $period, $page, $postsPerPage);

        return $this->respond($posts);
    }

    public function getRecommended($userId)
    {
        $userId = (int) $userId;
        $page = $this->request->getGet('page') ?: null;
        $postsPerPage = $this->request->getGet('posts-per-page') ?: null;

        $posts = $this->repository->getRecommendedPostsForUser($userId, $page, $postsPerPage);

        return $this->respond($posts);
    }

    public function submit()
    {
        $post = $this->request->getPost();

        $files = $this->request->getFiles();

        $uploadedFiles = [];

        if (!empty($files['attachments'])) {
            if (is_array($files['attachments'])) {
                foreach ($files['attachments'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $uploadedFiles[] = $file;
                    }
                }
            } else {
                $file = $files['attachments'];
                if ($file->isValid() && !$file->hasMoved()) {
                    $uploadedFiles[] = $file;
                }
            }
        }

        $postId = $this->postService->submitPost($post, ...$uploadedFiles);

        if (!$postId) {
            return $this->failValidationError('Invalid post data.');
        }

        return $this->respondCreated(['id' => $postId]);
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
