<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Community_Join_Request extends Migration
{
    public function up()
    {
        // community_join_request
        $this->forge->addField([
            'id_community' => ['type' => 'INT'],
            'id_user'      => ['type' => 'INT'],
            'requested_at' => ['type' => 'TIMESTAMP'],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
        ]);
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->createTable('community_join_request');
    }

    public function down()
    {
        $this->forge->dropTable('community_join_request');
    }
}
