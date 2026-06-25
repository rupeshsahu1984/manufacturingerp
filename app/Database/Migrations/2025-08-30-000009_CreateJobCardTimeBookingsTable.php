<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobCardTimeBookingsTable extends Migration
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
            'operator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'start_time' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'end_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'duration_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Duration in minutes',
            ],
            'activity_type' => [
                'type' => 'ENUM',
                'constraint' => ['setup', 'production', 'maintenance', 'break', 'idle', 'other'],
                'default' => 'production',
            ],
            'break_type' => [
                'type' => 'ENUM',
                'constraint' => ['lunch', 'tea', 'rest', 'maintenance', 'other'],
                'null' => true,
            ],
            'break_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('operator_id');
        $this->forge->addKey('start_time');
        $this->forge->addKey('activity_type');
        $this->forge->addKey('machine_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('job_card_id', 'job_cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('machine_id', 'machines', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tool_id', 'tools', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('job_card_time_bookings');
    }

    public function down()
    {
        $this->forge->dropTable('job_card_time_bookings');
    }
}
