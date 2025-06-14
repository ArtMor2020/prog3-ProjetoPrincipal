<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Attachment extends Migration
{
    public function up()
    {
        // attachment
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type'       => ['type' => 'ENUM', 'constraint' => ['IMAGE','VIDEO','GIF','DOCUMENT','ZIP','OTHER']],
            'path'       => ['type' => 'TEXT'],
            'is_deleted' => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('attachment');
    }

    public function down()
    {
        $this->forge->dropTable('attachment');
    }
}
