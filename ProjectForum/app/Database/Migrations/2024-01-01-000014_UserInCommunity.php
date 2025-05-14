<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserInCommunity extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user'      => ['type' => 'INT'],
            'id_community' => ['type' => 'INT'],
            'role'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_banned'    => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->createTable('user_in_community');
    }

    public function down()
    {
        $this->forge->dropTable('user_in_community');
    }
}