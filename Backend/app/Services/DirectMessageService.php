<?php

namespace App\Services;

use App\Repositories\DirectMessageRepository;

class DirectMessageService
{
    private DirectMessageRepository $directMessageRepository;

    public function __construct()
    {
        $this->directMessageRepository = new DirectMessageRepository();
    }

    public function sendMessage(array $data): int
    {

        return $this->directMessageRepository->sendMessage($data);
    }
}