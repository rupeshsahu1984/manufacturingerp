<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpenses extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
			'employee_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
			'cost_center_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
			'category' => [ 'type' => 'VARCHAR', 'constraint' => 64 ],
			'amount' => [ 'type' => 'DECIMAL', 'constraint' => '18,2' ],
			'currency' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
			'expense_date' => [ 'type' => 'DATE' ],
			'approval_status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'pending' ],
			'payment_status' => [ 'type' => 'VARCHAR', 'constraint' => 24, 'default' => 'unpaid' ],
			'notes' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
			'attachment_path' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
			'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
			'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('employee_id', 'employees', 'id', 'SET NULL', 'CASCADE');
		$this->forge->addForeignKey('cost_center_id', 'cost_centers', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('expenses');
	}

	public function down()
	{
		$this->forge->dropTable('expenses');
	}
}

