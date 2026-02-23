<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserSettingsTable extends Migration
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
                'type'     => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null'     => false,
            ],
            'openai_api_key' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'openai_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'gpt-4.1-nano',
            ],
            'agency_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'agency_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'agency_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
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
        $this->forge->createTable('user_settings');
    }

    public function down(): void
    {
        $this->forge->dropTable('user_settings', true);
    }
}
