<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Acos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'class'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'method'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'idheader'  => ['type' => 'BIGINT', 'default' => 0],
            // 'info'      => ['type' => 'TEXT', 'null' => true],
            // 'modifiedby' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'keterangan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // 'statusemkl' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('class');
        $this->forge->addKey('method');
        $this->forge->addKey('idheader');

        $this->forge->createTable('acos');
    }

    public function down()
    {
        $this->forge->dropTable('acos');
    }
}
