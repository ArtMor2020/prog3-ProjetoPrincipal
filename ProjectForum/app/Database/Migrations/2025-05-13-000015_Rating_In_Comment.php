<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RatingInComment extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_comment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'is_upvote' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
        ]);

        $this->forge->addPrimaryKey(['id_comment', 'id_user']);
        $this->forge->addForeignKey('id_comment', 'comment', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rating_in_comment');
    }

    public function down()
    {
        $this->forge->dropTable('rating_in_comment');
    }
}