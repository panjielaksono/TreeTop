<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Membership extends Migration
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
                // You might want to add a foreign key constraint here
                // 'null' => true, // Or false if a membership must have a user
            ],
            'subscription_type' => [ // e.g., 'Bulanan', 'Tahunan'
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],

            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
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
            // Add any other fields your membership needs (e.g., 'status', 'price', etc.)
        ]);
        $this->forge->addPrimaryKey('id');
        // If you have a 'users' table, you can add a foreign key
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('memberships');
    }

    public function down()
    {
        $this->forge->dropTable('memberships');
    }
}