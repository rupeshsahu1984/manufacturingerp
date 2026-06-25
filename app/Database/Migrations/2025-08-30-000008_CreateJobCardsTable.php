<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobCardsTable extends Migration
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
            'job_card_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'work_order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'operation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'planned_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'actual_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'scrap_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'rework_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'good_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'setup_time_planned' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Setup time in minutes',
            ],
            'setup_time_actual' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Actual setup time in minutes',
            ],
            'run_time_planned' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Run time per unit in minutes',
            ],
            'run_time_actual' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Actual run time per unit in minutes',
            ],
            'total_time_planned' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Total planned time in minutes',
            ],
            'total_time_actual' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Total actual time in minutes',
            ],
            'efficiency_pct' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'comment' => 'Efficiency percentage',
            ],
            'start_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'released', 'in_progress', 'completed', 'closed', 'cancelled'],
                'default' => 'draft',
            ],
            'operator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'workcenter_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'machine_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tool_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'quality_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'passed', 'failed', 'conditional'],
                'default' => 'pending',
            ],
            'qc_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'qc_passed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'qc_failed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attachments' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of attachment paths',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('job_card_number');
        $this->forge->addKey('work_order_id');
        $this->forge->addKey('operation_id');
        $this->forge->addKey('item_id');
        $this->forge->addKey('status');
        $this->forge->addKey('operator_id');
        $this->forge->addKey('workcenter_id');
        $this->forge->addKey('start_time');

        // Add foreign key constraints
        $this->forge->addForeignKey('work_order_id', 'work_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operation_id', 'bom_operations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('workcenter_id', 'workcenters', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('machine_id', 'machines', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tool_id', 'tools', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('job_cards');
    }

    public function down()
    {
        $this->forge->dropTable('job_cards');
    }
}
