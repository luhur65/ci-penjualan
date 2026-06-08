<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Error extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'kodeerror'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'   => ['type' => 'TEXT', 'null' => true],
            'modified_by'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            // 'info'        => ['type' => 'TEXT', 'null' => true],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('kodeerror');
        $this->forge->addKey('keterangan');

        $this->forge->createTable('errors');
    }

    public function down()
    {
        $this->forge->dropTable('errors');
    }
}
