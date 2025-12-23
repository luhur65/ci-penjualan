<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Menu extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'menuname'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'menu_seq'    => ['type' => 'BIGINT', 'default' => 0],
            'menu_parent' => ['type' => 'BIGINT', 'default' => 0],
            'menu_icon'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'aco_id'      => ['type' => 'BIGINT', 'unsigned' => true],
            'link'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'menuexe'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'menukode'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            // 'info'        => ['type' => 'TEXT', 'null' => true],
            // 'modifiedby'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('menu_parent');
        $this->forge->addKey('aco_id');
        $this->forge->addKey('menukode');

        $this->forge->createTable('menus');
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
