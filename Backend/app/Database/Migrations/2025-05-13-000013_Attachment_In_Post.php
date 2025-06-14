<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttachmentInPost extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_attachment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_post' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addPrimaryKey(['id_attachment', 'id_post']);
        $this->forge->addForeignKey('id_attachment', 'attachment', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_post', 'post', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attachment_in_post');
    }

    public function down()
    {
        $this->forge->dropTable('attachment_in_post');
    }
}