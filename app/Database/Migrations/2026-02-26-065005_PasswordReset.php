<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PasswordReset extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'username' => ['type' => 'VARCHAR', 'constraint' => 255],
            'token_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('username');
        $this->forge->addKey('expires_at');
        $this->forge->createTable('passwordresets');
    }

    public function down()
    {
        $this->forge->dropTable('passwordresets');
    }
}
