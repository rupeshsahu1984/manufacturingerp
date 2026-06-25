<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsUrgentToPurchaseOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_orders', [
            'is_urgent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => 'Flag to mark purchase order as urgent'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_orders', 'is_urgent');
    }
}
