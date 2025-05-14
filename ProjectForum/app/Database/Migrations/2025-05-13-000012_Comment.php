<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Comment extends Migration
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
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_parent_post', 'post', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_parent_comment', 'comment', 'id', 'CASCADE', 'CASCADE');   
        $this->forge->createTable('comment');
    }

    public function down()
    {
        $this->forge->dropTable('comment');
    }
}