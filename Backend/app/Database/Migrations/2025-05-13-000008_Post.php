<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Post extends Migration
{
    public function up()
    {
        // post
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_user'      => ['type' => 'INT', 'unsigned' => true,],
            'id_community' => ['type' => 'INT', 'unsigned' => true,],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'  => ['type' => 'TEXT'],
            'posted_at'    => ['type' => 'TIMESTAMP',],
            'updated_at'   => ['type' => 'TIMESTAMP', 'null' => true],
            'is_approved'  => ['type' => 'BOOLEAN'],
            'is_deleted'   => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_user', 'user', 'id');
        $this->forge->addForeignKey('id_community', 'community', 'id');
        $this->forge->createTable('post');
    }

    public function down()
    {
        $this->forge->dropTable('post');
    }
}
