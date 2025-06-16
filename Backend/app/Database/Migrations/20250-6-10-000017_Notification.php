<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [               // 'seen' or 'not_seen'
                'type' => 'VARCHAR',
                'constraint' => 16,
            ],
            'event_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'type' => [                // mention, message, pending_post, friend_request
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unsigned' => true,
            ],
            'id_origin' => [           // id of post, comment, person messaging, community with pending post, etc
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addPrimaryKey(['id']);
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification');
    }

    public function down()
    {
        $this->forge->dropTable('notification');
    }
}