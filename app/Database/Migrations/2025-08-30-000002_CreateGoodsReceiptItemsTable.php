<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoodsReceiptItemsTable extends Migration
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
            'goods_receipt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'purchase_order_item_id' => [
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
            'ordered_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'received_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'accepted_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'rejected_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0.000,
            ],
            'shortage_quantity' => [
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
            'quality_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'rejected', 'partial'],
                'default' => 'pending',
            ],
            'quality_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
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
        $this->forge->addKey('goods_receipt_id');
        $this->forge->addKey('purchase_order_item_id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('quality_status');

        // Add foreign key constraints
        $this->forge->addForeignKey('goods_receipt_id', 'goods_receipts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('purchase_order_item_id', 'purchase_order_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('goods_receipt_items');
    }

    public function down()
    {
        $this->forge->dropTable('goods_receipt_items');
    }
}
