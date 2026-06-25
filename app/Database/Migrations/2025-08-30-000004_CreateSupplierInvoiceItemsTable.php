<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierInvoiceItemsTable extends Migration
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
            'invoice_id' => [
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
        $this->forge->addKey('invoice_id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('purchase_order_item_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('invoice_id', 'supplier_invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('purchase_order_item_id', 'purchase_order_items', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('supplier_invoice_items');
    }

    public function down()
    {
        $this->forge->dropTable('supplier_invoice_items');
    }
}
