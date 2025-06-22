<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Throwable;

class SearchService
{
    protected CommunityRepository $communityRepository;
    protected UserRepository $userRepository;
    protected PostRepository $postRepository;

    public function __construct()
    {
        $this->communityRepository = new CommunityRepository();
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
    }

    public function search(string $query)
    {
        $query = trim($query);
        if (empty($query)) {
            return ['communities' => [], 'users' => [], 'posts' => []];
        }

        try {
            if (str_starts_with(strtolower($query), 'r/')) {
                $term = substr($query, 2);
                $communities = $this->communityRepository->searchByName($term) ?? [];
                return ['communities' => $communities, 'users' => [], 'posts' => []];
            }

            if (str_starts_with(strtolower($query), 'u/')) {
                $term = substr($query, 2);
                $users = $this->userRepository->getUsersByName($term) ?? [];
                return ['users' => $users, 'communities' => [], 'posts' => []];
            }

            $communitiesResult = $this->communityRepository->searchByName($query);
            $usersResult = $this->userRepository->getUsersByName($query);
            $postsResult = $this->postRepository->getPostsByTitle($query);

            return [
                'communities' => $communitiesResult ?? [],
                'users' => $usersResult ?? [],
                'posts' => $postsResult ?? [],
            ];
        } catch (Throwable $e) {
            log_message('error', '[SearchService->search()] Exceção: ' . $e->getMessage());
            return false;
        }
    }

    public function searchUsers(string $term): array|false
    {
        try {
            return $this->userRepository->getUsersByName($term) ?? [];
        } catch (\Throwable $e) {
            log_message('error', '[SearchService->searchUsers()] Exceção: ' . $e->getMessage());
            return false;
        }
    }
}