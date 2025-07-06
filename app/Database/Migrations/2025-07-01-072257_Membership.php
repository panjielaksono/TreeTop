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
            ],
            'subscription_type' => [ 
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
            'status' => [
                'type' => 'ENUM("AKTIF", "NON AKTIF")',
                'null' => false,
                'default' => 'AKTIF', 
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
        // Menambahkan foreign key
        $this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('memberships');
    }

    public function down()
    {
        $this->forge->dropTable('memberships');
    }
}
