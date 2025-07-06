<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transaction extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'after' => 'id'
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ],
            'total_harga' => [
                'type' => 'DOUBLE',
                'null' => FALSE,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => FALSE,
            ],
            'kelurahan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'after' => 'alamat'
            ],
            'ongkir' => [
                'type' => 'DOUBLE',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => FALSE,
            ],
            'expired_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'after' => 'status'
            ],
            'snap_token' => [ 
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'after' => 'kelurahan'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'after' => 'snap_token'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'after' => 'created_at'
            ]
        ]);
        
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('transaction');
    }

    public function down()
    {
        $this->forge->dropTable('transaction');
    }
}
