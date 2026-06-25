<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseReturnItemsTable extends Migration
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
            'purchase_return_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Reference to Purchase Return'
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Reference to Product'
            ],
            'quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Quantity returned'
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Unit price of the item'
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Total amount for this item'
            ],
            'return_reason' => [
                'type' => 'ENUM',
                'constraint' => ['excess', 'damage', 'qc_fail', 'wrong_item'],
                'null' => false,
                'comment' => 'Reason for returning this item'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes for this item'
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
        $this->forge->addKey('purchase_return_id');
        $this->forge->addKey('product_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('purchase_return_id', 'purchase_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('purchase_return_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_return_items', true);
    }
}
