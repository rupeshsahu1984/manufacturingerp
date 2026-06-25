<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndividualGSTFieldsToProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'cgst_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 9.00,
                'after' => 'gst_rate'
            ],
            'sgst_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 9.00,
                'after' => 'cgst_rate'
            ],
            'igst_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 18.00,
                'after' => 'sgst_rate'
            ]
        ]);
        
        // Update existing records to set default values
        $this->db->query("UPDATE products SET cgst_rate = gst_rate / 2, sgst_rate = gst_rate / 2, igst_rate = gst_rate WHERE gst_rate IS NOT NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['cgst_rate', 'sgst_rate', 'igst_rate']);
    }
}
