<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
            ],
            'event_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_origin' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
            ],
        ]);

        $this->forge->addPrimaryKey(['id']);
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification');
    }

    public function down()
    {
        $this->forge->dropTable('notification');
    }
}