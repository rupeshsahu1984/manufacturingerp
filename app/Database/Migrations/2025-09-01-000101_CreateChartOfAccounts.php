<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChartOfAccounts extends Migration
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
			'account_code' => [
				'type' => 'VARCHAR',
				'constraint' => 32,
			],
			'account_name' => [
				'type' => 'VARCHAR',
				'constraint' => 191,
			],
			'account_type' => [
				'type' => 'VARCHAR',
				'constraint' => 32,
				'comment' => 'asset, liability, equity, revenue, expense, bank, cash, ar, ap',
			],
			'parent_account_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true,
			],
			'currency' => [
				'type' => 'VARCHAR',
				'constraint' => 3,
				'null' => true,
			],
			'cost_center_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true,
			],
			'status' => [
				'type' => 'VARCHAR',
				'constraint' => 16,
				'default' => 'active',
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
		$this->forge->addKey('account_code');
		$this->forge->addKey('account_type');
		$this->forge->addForeignKey('parent_account_id', 'chart_of_accounts', 'id', 'SET NULL', 'CASCADE');

		$this->forge->createTable('chart_of_accounts');
	}

	public function down()
	{
		$this->forge->dropTable('chart_of_accounts');
	}
}

