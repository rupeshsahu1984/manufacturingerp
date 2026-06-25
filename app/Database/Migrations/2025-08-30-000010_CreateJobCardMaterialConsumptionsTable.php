<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobCardMaterialConsumptionsTable extends Migration
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
            'job_card_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'uom' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'unit_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'default' => 0,
            ],
            'total_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'default' => 0,
            ],
            'consumption_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'consumption_type' => [
                'type' => 'ENUM',
                'constraint' => ['planned', 'actual', 'scrap', 'rework'],
                'default' => 'actual',
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'warehouse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'location_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'operator_id' => [
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
            'scrap_qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,3',
                'default' => 0,
            ],
            'scrap_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('job_card_id');
        $this->forge->addKey('item_id');
        $this->forge->addKey('consumption_date');
        $this->forge->addKey('consumption_type');
        $this->forge->addKey('warehouse_id');
        $this->forge->addKey('operator_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('job_card_id', 'job_cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('location_id', 'warehouse_locations', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('machine_id', 'machines', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tool_id', 'tools', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('job_card_material_consumptions');
    }

    public function down()
    {
        $this->forge->dropTable('job_card_material_consumptions');
    }
}
