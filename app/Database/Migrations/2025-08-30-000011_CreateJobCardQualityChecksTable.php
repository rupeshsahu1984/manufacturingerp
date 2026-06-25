<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobCardQualityChecksTable extends Migration
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
            'check_type' => [
                'type' => 'ENUM',
                'constraint' => ['visual', 'dimensional', 'functional', 'material', 'other'],
                'default' => 'visual',
            ],
            'check_method' => [
                'type' => 'ENUM',
                'constraint' => ['100_percent', 'sampling', 'statistical', 'automated'],
                'default' => 'sampling',
            ],
            'check_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'inspector_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'sample_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Total population size',
            ],
            'sample_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Quantity sampled for inspection',
            ],
            'accepted_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'rejected_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'rework_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'scrap_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'defect_types' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of defect types',
            ],
            'defect_quantities' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of defect quantities',
            ],
            'quality_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'comment' => 'Quality score percentage',
            ],
            'tolerance_min' => [
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => true,
            ],
            'tolerance_max' => [
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => true,
            ],
            'actual_value' => [
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => true,
            ],
            'is_within_tolerance' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => null,
                'null' => true,
            ],
            'check_result' => [
                'type' => 'ENUM',
                'constraint' => ['pass', 'fail', 'conditional_pass'],
                'default' => 'pass',
            ],
            'check_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'approved', 'rejected'],
                'default' => 'pending',
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
        $this->forge->addKey('job_card_id');
        $this->forge->addKey('inspector_id');
        $this->forge->addKey('check_date');
        $this->forge->addKey('check_type');
        $this->forge->addKey('check_result');
        $this->forge->addKey('check_status');

        // Add foreign key constraints
        $this->forge->addForeignKey('job_card_id', 'job_cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('inspector_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('job_card_quality_checks');
    }

    public function down()
    {
        $this->forge->dropTable('job_card_quality_checks');
    }
}
