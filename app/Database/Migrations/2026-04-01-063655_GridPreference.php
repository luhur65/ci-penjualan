<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GridPreference extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK ke tabel users',
            ],
            'grid_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nama unik grid, misal: user_master_grid',
            ],
            'preferences' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'grid_name'], 'uq_user_grid');
        $this->forge->createTable('gridpreferences', true);
    }

    public function down()
    {
        $this->forge->dropTable('gridpreferences', true);
    }
}
