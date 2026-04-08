<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuditLogTrail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'module' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'record_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'old_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id', true);

        // Index (penting untuk performa)
        $this->forge->addKey('module');
        $this->forge->addKey('action');
        $this->forge->addKey('record_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('created_at');

        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs');
    }
}
