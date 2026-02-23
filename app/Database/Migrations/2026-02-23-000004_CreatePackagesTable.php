<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePackagesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'selected_services' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'starter_price' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'growth_price' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'premium_price' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'starter_services' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'growth_services' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'premium_services' => [
                'type' => 'JSON',
                'null' => true,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('packages');
    }

    public function down(): void
    {
        $this->forge->dropTable('packages', true);
    }
}
