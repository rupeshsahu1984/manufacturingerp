<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePurchaseOrdersTableStructure extends Migration
{
    public function up()
    {
        // Rename existing columns to match model expectations
        $this->forge->modifyColumn('purchase_orders', [
            'expected_delivery' => [
                'name' => 'expected_date',
                'type' => 'DATE',
                'null' => false
            ],
            'gst_amount' => [
                'name' => 'tax_amount',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => false
            ]
        ]);

        // Add missing columns
        $this->forge->addColumn('purchase_orders', [
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Payment terms for the purchase order'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes for the purchase order'
            ],
            'discount_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => false,
                'comment' => 'Discount amount applied to the order'
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User ID who last updated the record'
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User ID who approved the purchase order'
            ],
            'ordered_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User ID who placed the order'
            ],
            'received_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User ID who received the goods'
            ],
            'cancelled_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'User ID who cancelled the order'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when the order was approved'
            ],
            'ordered_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when the order was placed'
            ],
            'received_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when the goods were received'
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when the order was cancelled'
            ]
        ]);
    }

    public function down()
    {
        // Revert column renames
        $this->forge->modifyColumn('purchase_orders', [
            'expected_date' => [
                'name' => 'expected_delivery',
                'type' => 'DATE',
                'null' => false
            ],
            'tax_amount' => [
                'name' => 'gst_amount',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => false
            ]
        ]);

        // Drop added columns
        $this->forge->dropColumn('purchase_orders', [
            'payment_terms',
            'notes',
            'discount_amount',
            'updated_by',
            'approved_by',
            'ordered_by',
            'received_by',
            'cancelled_by',
            'approved_at',
            'ordered_at',
            'received_at',
            'cancelled_at'
        ]);
    }
}
