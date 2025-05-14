<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttachmentInComment extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_attachment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_comment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addPrimaryKey(['id_attachment', 'id_comment']);
        $this->forge->addForeignKey('id_attachment', 'attachments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_comment', 'comments', 'id', 'CASCADE', 'CASCADE');   
        $this->forge->createTable('attachment_in_comment');
    }

    public function down()
    {
        $this->forge->dropTable('attachment_in_comment');
    }
}