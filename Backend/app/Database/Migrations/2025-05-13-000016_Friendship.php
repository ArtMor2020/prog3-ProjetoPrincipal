<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Friendship extends Migration
{
    public function up()
    {
        $this->forge->addField([
            // --- CAMPO ADICIONADO ---
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            // ------------------------
            'id_user1' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_user2' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
            ],
            'requested_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'friends_since' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        // --- CHAVE PRIMÁRIA MODIFICADA ---
        $this->forge->addKey('id', true); 
        // Adicionamos um índice único para garantir que não haja pedidos duplicados
        $this->forge->addUniqueKey(['id_user1', 'id_user2']);
        // ---------------------------------
        
        $this->forge->addForeignKey('id_user1', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user2', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('friendship');
    }

    public function down()
    {
        $this->forge->dropTable('friendship');
    }
}