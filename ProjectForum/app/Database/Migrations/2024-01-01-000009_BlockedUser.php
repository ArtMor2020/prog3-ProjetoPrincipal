<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlockedUser extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user'         => ['type' => 'INT'],
            'id_blocked_user' => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->addForeignKey('id_blocked_user', 'user', 'id');
        $this->forge->createTable('blocked_user');
    }

    public function down()
    {
        $this->forge->dropTable('blocked_user');
    }
}