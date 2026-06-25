<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'department_name' => 'Production',
                'description' => 'Manufacturing and production operations',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'Quality Control',
                'description' => 'Quality assurance and control operations',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'Sales',
                'description' => 'Sales and customer relations',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'Finance',
                'description' => 'Financial management and accounting',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'Human Resources',
                'description' => 'HR management and employee relations',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'IT',
                'description' => 'Information technology and systems',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'department_name' => 'Logistics',
                'description' => 'Supply chain and logistics management',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
