<?php

namespace App\Services;

class Notification
{
    public function getNotifications(int $userId)
    {
        // pending posts for communities where ADM, as "community has x pending posts"
        // direct messages
        // friendship requests
        // mentions in posts and comments, make 'name' in 'user' a unique id
    }
}