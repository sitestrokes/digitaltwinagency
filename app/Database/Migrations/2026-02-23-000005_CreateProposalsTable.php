<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProposalsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => false,
            ],
            'prospect_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'package_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'agency_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'contact_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'niche' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tier' => [
                'type'       => 'ENUM',
                'constraint' => ['starter', 'growth', 'premium', 'custom'],
                'default'    => 'growth',
            ],
            'price' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'services' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'generation_mode' => [
                'type'       => 'ENUM',
                'constraint' => ['ai', 'template'],
                'default'    => 'template',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valid_until' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'sent', 'accepted', 'rejected'],
                'default'    => 'draft',
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
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('proposals');
    }

    public function down(): void
    {
        $this->forge->dropTable('proposals', true);
    }
}
