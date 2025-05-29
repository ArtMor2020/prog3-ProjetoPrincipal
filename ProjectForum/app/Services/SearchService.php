<?php

namespace App\Services;

use App\Repositories\CommunityRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

class SearchService
{
    protected CommunityRepository $communityRepository;
    protected UserRepository $userRepository;
    protected PostRepository $postRepository;

    public function __construct(){
        $this->communityRepository = new CommunityRepository();
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
    }

    public function search(string $query){
        
        try{
            $result = [
                'communities' => $this->communityRepository->getCommunitiesByName($query),
                'users' => $this->userRepository->getUsersByName($query),
                'posts' => $this->postRepository->getPostsByTitle($query)
        ];
        } catch (\Throwable $e) {
            log_message('error', '[SearchService->Search()] ' . $e->getMessage());
            return false;
        }
        return $result;
    }
}