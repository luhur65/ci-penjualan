<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Acl extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'aco_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'role_id' => ['type' => 'BIGINT', 'unsigned' => true],
            // 'info' => ['type' => 'TEXT', 'null' => true],
            // 'modifiedby' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('aco_id');
        $this->forge->addKey('role_id');

        $this->forge->createTable('acl');
    }

    public function down()
    {
        $this->forge->dropTable('acl');
    }
}
