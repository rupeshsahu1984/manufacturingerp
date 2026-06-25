<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankAndReconciliation extends Migration
{
	public function up()
	{
		// bank_accounts
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'account_name' => [ 'type' => 'VARCHAR', 'constraint' => 128 ],
			'bank_name' => [ 'type' => 'VARCHAR', 'constraint' => 128, 'null' => true ],
			'account_number' => [ 'type' => 'VARCHAR', 'constraint' => 64, 'null' => true ],
			'ifsc' => [ 'type' => 'VARCHAR', 'constraint' => 32, 'null' => true ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'coa_account_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
			'is_active' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 1 ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('coa_account_id', 'chart_of_accounts', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('bank_accounts');

		// bank_reconciliations
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'bank_account_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'statement_date' => [ 'type' => 'DATE' ],
			'system_balance' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'bank_balance' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'difference' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'open' ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('bank_account_id', 'bank_accounts', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('bank_reconciliations');
	}

	public function down()
	{
		$this->forge->dropTable('bank_reconciliations');
		$this->forge->dropTable('bank_accounts');
	}
}

