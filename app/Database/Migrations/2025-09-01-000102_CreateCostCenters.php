<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCostCenters extends Migration
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
			'code' => [
				'type' => 'VARCHAR',
				'constraint' => 32,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 191,
			],
			'parent_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'budget_amount' => [
				'type' => 'DECIMAL',
				'constraint' => '18,2',
				'default' => '0.00',
			],
			'currency' => [
				'type' => 'VARCHAR',
				'constraint' => 3,
				'null' => true,
			],
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
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
		$this->forge->addKey('code');
		$this->forge->addForeignKey('parent_id', 'cost_centers', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('cost_centers');
	}

	public function down()
	{
		$this->forge->dropTable('cost_centers');
	}
}

