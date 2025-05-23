<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PostView extends Migration
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
            'viewed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        //PK
        $this->forge->addPrimaryKey(['id_post', 'id_user']);

        // Foreign Keys
        $this->forge->addForeignKey('id_post', 'post', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('post_view');
    }

    public function down()
    {
        $this->forge->dropTable('post_view');
    }
}