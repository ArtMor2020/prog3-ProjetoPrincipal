<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Community extends Migration
{
    public function up()
    {
        // community
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT'],
            'id_owner'    => ['type' => 'INT'],
            'is_private'  => ['type' => 'BOOLEAN'],
            'is_deleted'  => ['type' => 'BOOLEAN'],
            'is_banned'   => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_owner', 'user', 'id');
        $this->forge->createTable('community');
    }

    public function down()
    {
        $this->forge->dropTable('community');
    }
}
