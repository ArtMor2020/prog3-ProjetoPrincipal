<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitialMigration extends Migration
{
    public function up()
    {
        // community_view
        $this->forge->addField([
            'id_community' => ['type' => 'INT', 'unsigned' => true,],
            'id_user'      => ['type' => 'INT', 'unsigned' => true,],
            'viewed_at'     => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey(['id_user', 'id_community']);
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->createTable('community_view');
    }

    public function down()
    {
        $this->forge->dropTable('community_view');
    }
}
