<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHSNAndGSTToProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'hsn_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'unit_price'
            ],
            'gst_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 18.00,
                'after' => 'hsn_code'
            ],
            'selling_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'gst_rate'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['hsn_code', 'gst_rate', 'selling_price']);
    }
}
