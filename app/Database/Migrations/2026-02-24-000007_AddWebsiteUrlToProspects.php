<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds website_url column to the prospects table.
 *
 * This migration is safe to run on an existing deployment that was set up
 * before this column was introduced. The column is added after `name` and
 * uses IF NOT EXISTS logic in the down() method to stay idempotent.
 */
class AddWebsiteUrlToProspects extends Migration
{
    public function up(): void
    {
        // Only add the column if it does not already exist
        $fields = $this->db->getFieldNames('prospects');
        if (! in_array('website_url', $fields, true)) {
            $this->forge->addColumn('prospects', [
                'website_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 512,
                    'null'       => true,
                    'after'      => 'name',
                ],
            ]);
        }
    }

    public function down(): void
    {
        $fields = $this->db->getFieldNames('prospects');
        if (in_array('website_url', $fields, true)) {
            $this->forge->dropColumn('prospects', 'website_url');
        }
    }
}
