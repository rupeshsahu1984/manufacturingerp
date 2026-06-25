<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDebitNotesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'debit_note_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'debit_note_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'purchase_order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'goods_receipt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'return_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'gst_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'processed', 'cancelled'],
                'default' => 'draft',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('debit_note_number');
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('purchase_order_id');
        $this->forge->addKey('goods_receipt_id');
        $this->forge->addKey('invoice_id');
        $this->forge->addKey('status');

        // Add foreign key constraints
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('purchase_order_id', 'purchase_orders', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('goods_receipt_id', 'goods_receipts', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'supplier_invoices', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('debit_notes');
    }

    public function down()
    {
        $this->forge->dropTable('debit_notes');
    }
}
