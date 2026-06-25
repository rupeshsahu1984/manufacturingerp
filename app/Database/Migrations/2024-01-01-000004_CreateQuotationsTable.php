<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
                'auto_increment' => true,
            ],
            'quotation_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
            ],
            'quotation_date' => [
                'type' => 'DATE',
            ],
            'valid_until' => [
                'type' => 'DATE',
            ],
            'delivery_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'gst_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'sent', 'accepted', 'rejected', 'expired'],
                'default' => 'draft',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('customer_id');
        $this->forge->addKey('status');
        $this->forge->addKey('quotation_date');

        // Add foreign key constraints
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('quotations');
    }

    public function down()
    {
        $this->forge->dropTable('quotations');
    }
}
