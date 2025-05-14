<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CommunityView extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_community' => ['type' => 'INT'],
            'id_user'      => ['type' => 'INT'],
            'viewd_at'     => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->createTable('community_view');
    }

    public function down()
    {
        $this->forge->dropTable('community_view');
    }
}