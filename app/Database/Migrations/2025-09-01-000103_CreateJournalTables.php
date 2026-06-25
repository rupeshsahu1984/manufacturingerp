<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalTables extends Migration
{
	public function up()
	{
		// journal_entries
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'journal_number' => [ 'type' => 'VARCHAR', 'constraint' => 32 ],
			'entry_date' => [ 'type' => 'DATE' ],
			'description' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'fx_rate' => [ 'type' => 'DECIMAL', 'constraint' => '18,6', 'default' => '1.000000' ],
			'reference_module' => [ 'type' => 'VARCHAR', 'constraint' => 64, 'null' => true ],
			'reference_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'status' => [ 'type' => 'VARCHAR', 'constraint' => 16, 'default' => 'posted' ],
			'created_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'approved_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('journal_number');
		$this->forge->createTable('journal_entries');

		// journal_lines
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'journal_entry_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'line_no' => [ 'type' => 'INT', 'constraint' => 11 ],
			'account_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
			'cost_center_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
			'description' => [ 'type' => 'VARCHAR', 'constraint' => 191, 'null' => true ],
			'debit' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'credit' => [ 'type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00' ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey(['journal_entry_id', 'line_no']);
		$this->forge->addForeignKey('journal_entry_id', 'journal_entries', 'id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('account_id', 'chart_of_accounts', 'id', 'RESTRICT', 'CASCADE');
		$this->forge->addForeignKey('cost_center_id', 'cost_centers', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('journal_lines');
	}

	public function down()
	{
		$this->forge->dropTable('journal_lines');
		$this->forge->dropTable('journal_entries');
	}
}

