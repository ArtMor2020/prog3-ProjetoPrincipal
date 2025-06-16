<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
{
    public function up()
    {
        // user
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'password'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'about'       => ['type' => 'TEXT'],
            'is_private'  => ['type' => 'BOOLEAN'],
            'is_banned'   => ['type' => 'BOOLEAN'],
            'is_deleted'  => ['type' => 'BOOLEAN'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user');
    
    }

    public function down(){
                $this->forge->dropTable('user');
    }
}