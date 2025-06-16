<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'AdminUser',
                'email' => 'admin@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'about' => 'Administrator of the system.',
                'is_private' => false,
                'is_banned' => false,
                'is_deleted' => false,
            ],
            [
                'name' => 'JohnDoe',
                'email' => 'john.doe@example.com',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'about' => 'Just a test user.',
                'is_private' => false,
                'is_banned' => false,
                'is_deleted' => false,
            ],
        ];

        $this->db->table('user')->insertBatch($data);
    }
}

class CommunitySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'General',
                'description' => 'General discussion forum',
                'id_owner' => 1,
                'is_private' => false,
                'is_deleted' => false,
                'is_banned' => false,
            ],
            [
                'name' => 'Announcements',
                'description' => 'Official announcements and news',
                'id_owner' => 1,
                'is_private' => false,
                'is_deleted' => false,
                'is_banned' => false,
            ],
        ];

        $this->db->table('community')->insertBatch($data);
    }
}

class PostSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user' => 2,
                'id_community' => 1,
                'title' => 'Welcome to the General Forum',
                'description' => 'Feel free to post anything here.',
                'posted_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'is_approved' => true,
                'is_deleted' => false,
            ],
            [
                'id_user' => 2,
                'id_community' => 2,
                'title' => 'System Launch!',
                'description' => 'We are live as of today!',
                'posted_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'is_approved' => true,
                'is_deleted' => false,
            ],
        ];

        $this->db->table('post')->insertBatch($data);
    }
}

class BlockedUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_user' => 1, 'id_blocked_user' => 2],
            ['id_user' => 2, 'id_blocked_user' => 1],
        ];
        $this->db->table('blocked_user')->insertBatch($data);
    }
}

class DirectMessageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_sender' => 1,
                'id_reciever' => 2,
                'content' => 'Hello John, welcome!',
                'sent_at' => date('Y-m-d H:i:s'),
                'is_seen' => false,
                'is_deleted' => false,
            ],
        ];
        $this->db->table('direct_message')->insertBatch($data);
    }
}

class UserInCommunitySeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_user' => 1, 'id_community' => 1, 'role' => 'ADMIN', 'is_banned' => false],
            ['id_user' => 2, 'id_community' => 1, 'role' => 'MEMBER', 'is_banned' => false],
            ['id_user' => 2, 'id_community' => 2, 'role' => 'MODERATOR', 'is_banned' => false],
        ];
        $this->db->table('user_in_community')->insertBatch($data);
    }
}

class CommunityViewSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_community' => 1, 'id_user' => 1, 'viewed_at' => date('Y-m-d H:i:s')],
            ['id_community' => 2, 'id_user' => 2, 'viewed_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('community_view')->insertBatch($data);
    }
}

class CommunityJoinRequestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_community' => 2, 'id_user' => 1, 'requested_at' => date('Y-m-d H:i:s'), 'status' => 'pending'],
        ];
        $this->db->table('community_join_request')->insertBatch($data);
    }
}

class RatingInPostSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_post' => 1, 'id_user' => 2, 'is_upvote' => true],
            ['id_post' => 2, 'id_user' => 1, 'is_upvote' => false],
        ];
        $this->db->table('rating_in_post')->insertBatch($data);
    }
}

class PostViewSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_post' => 1, 'id_user' => 2, 'viewed_at' => date('Y-m-d H:i:s')],
            ['id_post' => 2, 'id_user' => 1, 'viewed_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('post_view')->insertBatch($data);
    }
}

class AttachmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['type' => 'IMAGE', 'path' => '/uploads/image1.png', 'is_deleted' => false],
            ['type' => 'DOCUMENT', 'path' => '/uploads/doc1.pdf', 'is_deleted' => false],
        ];
        $this->db->table('attachment')->insertBatch($data);
    }
}

class AttachmentInPostSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_attachment' => 1, 'id_post' => 1],
            ['id_attachment' => 2, 'id_post' => 2],
        ];
        $this->db->table('attachment_in_post')->insertBatch($data);
    }
}

class AttachmentInCommentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_attachment' => 1, 'id_comment' => 1],
        ];
        $this->db->table('attachment_in_comment')->insertBatch($data);
    }
}

class RatingInCommentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_comment' => 1, 'id_user' => 2, 'is_upvote' => true],
            ['id_comment' => 2, 'id_user' => 1, 'is_upvote' => false],
        ];
        $this->db->table('rating_in_comment')->insertBatch($data);
    }
}

class CommentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user' => 2,
                'id_parent_post' => 1,
                'id_parent_comment' => null,
                'content' => 'Ótimo post! Obrigado por compartilhar.',
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'id_user' => 1,
                'id_parent_post' => 1,
                'id_parent_comment' => 1,
                'content' => 'Que bom que gostou! 😊',
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
        ];

        $this->db->table('comment')->insertBatch($data);
    }
}

class FriendshipSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user1' => 1,
                'id_user2' => 2,
                'status' => 'friends',
                'requested_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'friends_since'=> date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('friendship')->insertBatch($data);
    }
}

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user'    => 1,
                'status'     => 'not_seen',
                'event_date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'type'       => 'mention_in_post',
                'id_origin'  => 1, // post id
            ],
            [
                'id_user'    => 2,
                'status'     => 'seen',
                'event_date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'type'       => 'friend_request',
                'id_origin'  => 1, // user id
            ],
            [
                'id_user'    => 3,
                'status'     => 'not_seen',
                'event_date' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'type'       => 'message',
                'id_origin'  => 2, // user id
            ],
            [
                'id_user'    => 1,
                'status'     => 'seen',
                'event_date' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'type'       => 'invite',
                'id_origin'  => 1, // community id
            ],
            [
                'id_user'    => 2,
                'status'     => 'not_seen',
                'event_date' => date('Y-m-d H:i:s', strtotime('-6 hours')),
                'type'       => 'mention_in_comment',
                'id_origin'  => 1, // comment id
            ],
        ];

        $this->db->table('notification')->insertBatch($data);
    }
}

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\UserSeeder');
        $this->call('App\Database\Seeds\CommunitySeeder');
        $this->call('App\Database\Seeds\PostSeeder');
        $this->call('App\Database\Seeds\CommentSeeder');
        $this->call('App\Database\Seeds\BlockedUserSeeder');
        $this->call('App\Database\Seeds\DirectMessageSeeder');
        $this->call('App\Database\Seeds\UserInCommunitySeeder');
        $this->call('App\Database\Seeds\CommunityViewSeeder');
        $this->call('App\Database\Seeds\CommunityJoinRequestSeeder');
        $this->call('App\Database\Seeds\RatingInPostSeeder');
        $this->call('App\Database\Seeds\PostViewSeeder');
        $this->call('App\Database\Seeds\AttachmentSeeder');
        $this->call('App\Database\Seeds\AttachmentInPostSeeder');
        $this->call('App\Database\Seeds\AttachmentInCommentSeeder');
        $this->call('App\Database\Seeds\RatingInCommentSeeder');
        $this->call('App\Database\Seeds\FriendshipSeeder');
        $this->call('App\Database\Seeds\NotificationSeeder');
    }
}