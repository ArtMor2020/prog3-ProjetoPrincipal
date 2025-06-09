<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Friendship extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user1' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_user2' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [               // 'friends' or 'friend_request'
                'type' => 'VARCHAR',
                'constraint' => 16,
            ],
            'requested_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'friends_since' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey(['id']);
        $this->forge->addForeignKey('id_user1', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user2', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('friendship');
    }

    public function down()
    {
        $this->forge->dropTable('friendship');
    }
}