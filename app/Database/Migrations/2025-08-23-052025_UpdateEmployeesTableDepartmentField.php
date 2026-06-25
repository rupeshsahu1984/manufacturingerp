<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEmployeesTableDepartmentField extends Migration
{
    public function up()
    {
        // Add department_id field
        $this->forge->addColumn('employees', [
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'department'
            ]
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Remove foreign key constraint
        $this->forge->dropForeignKey('employees', 'employees_department_id_foreign');
        
        // Remove department_id column
        $this->forge->dropColumn('employees', 'department_id');
    }
}
