<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RatingInPost extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_post' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'is_upvote' => [
                'type' => 'BOOLEAN',
            ],
        ]);

        // Chave primária composta
        $this->forge->addPrimaryKey(['id_post', 'id_user']);
        
        // Foreign Keys
        $this->forge->addForeignKey('id_post', 'post', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('rating_in_post');
    }

    public function down()
    {
        $this->forge->dropTable('rating_in_post');
    }
}