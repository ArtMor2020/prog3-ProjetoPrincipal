<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDirectMessageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'id_sender'   => ['type' => 'INT'],
            'id_reciever' => ['type' => 'INT'],
            'content'     => ['type' => 'TEXT'],
            'sent_at'     => ['type' => 'TIMESTAMP', 'null' => true],
            'is_seen'     => ['type' => 'BOOLEAN'],
            'is_deleted'  => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_sender', 'user', 'id');
        $this->forge->addForeignKey('id_reciever', 'user', 'id');
        $this->forge->createTable('direct_message');
    }

    public function down()
    {
        $this->forge->dropTable('direct_message');
    }
}