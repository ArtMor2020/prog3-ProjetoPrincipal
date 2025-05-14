<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CommunityJoinRequest extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_parent_post' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, 
            ],
            'id_parent_comment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, 
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'is_deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0, 
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_parent_post', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_parent_comment', 'comments', 'id', 'CASCADE', 'CASCADE');   
        $this->forge->createTable('comments');
    }

    public function down()
    {
        $this->forge->dropTable('comments');
    }
}