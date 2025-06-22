<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowNullCommunityInPosts extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('post', [
            'id_community' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropForeignKey('post', 'post_id_community_foreign');
        $this->forge->modifyColumn('post', [
            'id_community' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
        ]);
        $this->forge->addForeignKey('id_community', 'community', 'id', 'CASCADE', 'CASCADE');
    }
}