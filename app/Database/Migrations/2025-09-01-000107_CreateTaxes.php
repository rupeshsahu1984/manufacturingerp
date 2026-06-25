<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaxes extends Migration
{
	public function up()
	{
		// tax_definitions
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'tax_code' => [ 'type' => 'VARCHAR', 'constraint' => 32 ],
			'tax_name' => [ 'type' => 'VARCHAR', 'constraint' => 128 ],
			'tax_type' => [ 'type' => 'VARCHAR', 'constraint' => 32, 'comment' => 'GST, VAT, Service, TDS, TCS' ],
			'rate' => [ 'type' => 'DECIMAL', 'constraint' => '9,4', 'default' => '0.0000' ],
			'jurisdiction' => [ 'type' => 'VARCHAR', 'constraint' => 64, 'null' => true ],
			'is_active' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 1 ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('tax_code');
		$this->forge->createTable('tax_definitions');

		// invoice_taxes (generic for AR/AP)
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'module' => [ 'type' => 'VARCHAR', 'constraint' => 16, 'comment' => 'AR or AP' ],
			'invoice_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'tax_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'tax_rate' => [ 'type' => 'DECIMAL', 'constraint' => '9,4', 'default' => '0.0000' ],
			'tax_amount' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'calculated' ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('tax_id', 'tax_definitions', 'id', 'RESTRICT', 'CASCADE');
		$this->forge->createTable('invoice_taxes');
	}

	public function down()
	{
		$this->forge->dropTable('invoice_taxes');
		$this->forge->dropTable('tax_definitions');
	}
}

