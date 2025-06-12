<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User_In_Community extends Migration
{
    public function up()
    {
        // user_in_community
        $this->forge->addField([
            'id_user'      => ['type' => 'INT', 'unsigned' => true,],
            'id_community' => ['type' => 'INT', 'unsigned' => true,],
            'role'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_banned'    => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addPrimaryKey(['id_user', 'id_community']);
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->createTable('user_in_community');
    }

    public function down()
    {
        $this->forge->dropTable('user_in_community');
    }
}
