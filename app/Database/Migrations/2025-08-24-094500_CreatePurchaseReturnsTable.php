<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseReturnsTable extends Migration
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
            'prn_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
                'comment' => 'Purchase Return Number'
            ],
            'grn_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Reference to Goods Receipt Note'
            ],
            'bill_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Reference to Purchase Bill'
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Reference to Supplier'
            ],
            'return_date' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Date of return'
            ],
            'return_reason' => [
                'type' => 'ENUM',
                'constraint' => ['excess', 'damage', 'qc_fail', 'wrong_item'],
                'null' => false,
                'comment' => 'Reason for return'
            ],
            'total_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => false,
                'comment' => 'Total quantity returned'
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => false,
                'comment' => 'Total amount of return'
            ],
            'is_urgent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => 'Flag to mark return as urgent'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'approved', 'sent', 'received'],
                'default' => 'draft',
                'null' => false,
                'comment' => 'Status of the return'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes'
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'User who created the return'
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User who last updated the return'
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User who approved the return'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when approved'
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
        $this->forge->addKey('prn_number', false, true);
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('grn_id');
        $this->forge->addKey('bill_id');
        $this->forge->addKey('status');

        // Add foreign key constraints
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('grn_id', 'goods_receipt_notes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('bill_id', 'purchase_bills', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('purchase_returns', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_returns', true);
    }
}
