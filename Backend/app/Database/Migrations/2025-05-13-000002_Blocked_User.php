<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Blocked_User extends Migration
{
    public function up()
    {
        // blocked_user
        $this->forge->addField([
            'id_user'         => ['type' => 'INT', 'unsigned' => true,],
            'id_blocked_user' => ['type' => 'INT', 'unsigned' => true,],
        ]);
        $this->forge->addPrimaryKey(['id_user', 'id_blocked_user']);
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->addForeignKey('id_blocked_user', 'user', 'id');
        $this->forge->createTable('blocked_user');
    }

    public function down()
    {
        $this->forge->dropTable('blocked_user');
    }
}
