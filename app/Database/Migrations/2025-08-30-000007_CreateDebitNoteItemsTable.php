<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDebitNoteItemsTable extends Migration
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
            'debit_note_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'purchase_order_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'goods_receipt_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'unit_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'gst_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'gst_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'line_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'return_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'return_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('debit_note_id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('purchase_order_item_id');
        $this->forge->addKey('goods_receipt_item_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('debit_note_id', 'debit_notes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('purchase_order_item_id', 'purchase_order_items', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('goods_receipt_item_id', 'goods_receipt_items', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('debit_note_items');
    }

    public function down()
    {
        $this->forge->dropTable('debit_note_items');
    }
}
