<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAR_AP_Tables extends Migration
{
	public function up()
	{
		// Accounts Receivable
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'customer_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'invoice_no' => [ 'type' => 'VARCHAR', 'constraint' => 64 ],
			'invoice_date' => [ 'type' => 'DATE' ],
			'due_date' => [ 'type' => 'DATE', 'null' => true ],
			'amount' => [ 'type' => 'DECIMAL', 'constraint' => '18,2' ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'open' ],
			'reference_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('invoice_no');
		$this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'CASCADE');
		$this->forge->createTable('accounts_receivable');

		// Accounts Payable
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'supplier_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'invoice_no' => [ 'type' => 'VARCHAR', 'constraint' => 64 ],
			'invoice_date' => [ 'type' => 'DATE' ],
			'due_date' => [ 'type' => 'DATE', 'null' => true ],
			'amount' => [ 'type' => 'DECIMAL', 'constraint' => '18,2' ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'open' ],
			'reference_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('invoice_no');
		$this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'RESTRICT', 'CASCADE');
		$this->forge->createTable('accounts_payable');
	}

	public function down()
	{
		$this->forge->dropTable('accounts_payable');
		$this->forge->dropTable('accounts_receivable');
	}
}

