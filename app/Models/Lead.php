<?php

namespace App\Models;

use CodeIgniter\Model;

class Lead extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'lead_code', 'lead_name', 'contact_person', 'email', 'phone', 'company',
        'source', 'status', 'assigned_to', 'expected_value', 'notes',
        'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'lead_name' => 'required|min_length[3]|max_length[255]',
        'contact_person' => 'required|min_length[2]|max_length[100]',
        'email' => 'required|valid_email|max_length[255]',
        'phone' => 'required|min_length[10]|max_length[15]',
        'source' => 'required|in_list[website,referral,cold_call,social_media,exhibition,other]',
        'status' => 'required|in_list[new,contacted,qualified,proposal_sent,negotiation,won,lost]',
        'assigned_to' => 'required|integer'
    ];

    protected $validationMessages = [
        'lead_name' => [
            'required' => 'Lead name is required',
            'min_length' => 'Lead name must be at least 3 characters long',
            'max_length' => 'Lead name cannot exceed 255 characters'
        ],
        'contact_person' => [
            'required' => 'Contact person is required',
            'min_length' => 'Contact person must be at least 2 characters long',
            'max_length' => 'Contact person cannot exceed 100 characters'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'max_length' => 'Email cannot exceed 255 characters'
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'min_length' => 'Phone number must be at least 10 digits',
            'max_length' => 'Phone number cannot exceed 15 digits'
        ],
        'source' => [
            'required' => 'Lead source is required',
            'in_list' => 'Please select a valid lead source'
        ],
        'status' => [
            'required' => 'Lead status is required',
            'in_list' => 'Please select a valid lead status'
        ],
        'assigned_to' => [
            'required' => 'Assigned user is required',
            'integer' => 'Please select a valid user'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateLeadCode'];
    protected $beforeUpdate = [];

    /**
     * Generate unique lead code
     */
    protected function generateLeadCode(array $data)
    {
        if (!isset($data['data']['lead_code']) || empty($data['data']['lead_code'])) {
            $data['data']['lead_code'] = $this->generateUniqueCode();
        }
        return $data;
    }

    /**
     * Generate unique lead code
     */
    public function generateUniqueCode()
    {
        $prefix = 'LEAD';
        $year = date('Y');
        $month = date('m');
        
        // Get the last lead code for this month
        $lastLead = $this->where('lead_code LIKE', $prefix . $year . $month . '%')
                        ->orderBy('lead_code', 'DESC')
                        ->first();
        
        if ($lastLead) {
            $lastNumber = intval(substr($lastLead['lead_code'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get leads with details
     */
    public function getLeadsWithDetails($filters = [])
    {
        $builder = $this->db->table('leads l')
                           ->select('l.*, u.name as assigned_user_name')
                           ->join('users u', 'u.id = l.assigned_to', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('l.status', $filters['status']);
        }
        if (!empty($filters['source'])) {
            $builder->where('l.source', $filters['source']);
        }
        if (!empty($filters['assigned_to'])) {
            $builder->where('l.assigned_to', $filters['assigned_to']);
        }

        return $builder->orderBy('l.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get lead statistics
     */
    public function getLeadStats()
    {
        $stats = [
            'total_leads' => $this->countAll(),
            'new_leads' => $this->where('status', 'new')->countAllResults(),
            'contacted_leads' => $this->where('status', 'contacted')->countAllResults(),
            'qualified_leads' => $this->where('status', 'qualified')->countAllResults(),
            'won_leads' => $this->where('status', 'won')->countAllResults(),
            'lost_leads' => $this->where('status', 'lost')->countAllResults()
        ];

        // Calculate conversion rates
        $totalLeads = $stats['total_leads'];
        if ($totalLeads > 0) {
            $stats['conversion_rate'] = round(($stats['won_leads'] / $totalLeads) * 100, 2);
            $stats['qualification_rate'] = round(($stats['qualified_leads'] / $totalLeads) * 100, 2);
        } else {
            $stats['conversion_rate'] = 0;
            $stats['qualification_rate'] = 0;
        }

        return $stats;
    }

    /**
     * Get leads by source
     */
    public function getLeadsBySource()
    {
        return $this->select('source, COUNT(*) as count')
                   ->groupBy('source')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Get leads by status
     */
    public function getLeadsByStatus()
    {
        return $this->select('status, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Get leads by assigned user
     */
    public function getLeadsByUser()
    {
        return $this->db->table('leads l')
                       ->select('u.name as user_name, COUNT(l.id) as lead_count')
                       ->join('users u', 'u.id = l.assigned_to', 'left')
                       ->groupBy('l.assigned_to, u.name')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get recent leads
     */
    public function getRecentLeads($limit = 10)
    {
        return $this->db->table('leads l')
                       ->select('l.*, u.name as assigned_user_name')
                       ->join('users u', 'u.id = l.assigned_to', 'left')
                       ->orderBy('l.created_at', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Update lead status
     */
    public function updateLeadStatus($leadId, $status, $userId = null)
    {
        $data = ['status' => $status];
        if ($userId) {
            $data['updated_by'] = $userId;
        }
        
        return $this->update($leadId, $data);
    }

    /**
     * Assign lead to user
     */
    public function assignLead($leadId, $userId, $assignedBy = null)
    {
        $data = ['assigned_to' => $userId];
        if ($assignedBy) {
            $data['updated_by'] = $assignedBy;
        }
        
        return $this->update($leadId, $data);
    }

    /**
     * Convert lead to customer
     */
    public function convertToCustomer($leadId, $customerData)
    {
        $this->db->transStart();
        
        // Update lead status to won
        $this->updateLeadStatus($leadId, 'won');
        
        // Create customer record
        $customerModel = new Customer();
        $customerId = $customerModel->createCustomer($customerData);
        
        $this->db->transComplete();
        
        return $this->db->transStatus() ? $customerId : false;
    }

    /**
     * Get lead pipeline
     */
    public function getLeadPipeline()
    {
        $pipeline = [
            'new' => $this->where('status', 'new')->countAllResults(),
            'contacted' => $this->where('status', 'contacted')->countAllResults(),
            'qualified' => $this->where('status', 'qualified')->countAllResults(),
            'proposal_sent' => $this->where('status', 'proposal_sent')->countAllResults(),
            'negotiation' => $this->where('status', 'negotiation')->countAllResults(),
            'won' => $this->where('status', 'won')->countAllResults(),
            'lost' => $this->where('status', 'lost')->countAllResults()
        ];

        return $pipeline;
    }

    /**
     * Get lead value by status
     */
    public function getLeadValueByStatus()
    {
        return $this->select('status, SUM(expected_value) as total_value, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Search leads
     */
    public function searchLeads($searchTerm)
    {
        return $this->db->table('leads l')
                       ->select('l.*, u.name as assigned_user_name')
                       ->join('users u', 'u.id = l.assigned_to', 'left')
                       ->groupStart()
                       ->like('l.lead_name', $searchTerm)
                       ->orLike('l.contact_person', $searchTerm)
                       ->orLike('l.email', $searchTerm)
                       ->orLike('l.phone', $searchTerm)
                       ->orLike('l.company', $searchTerm)
                       ->groupEnd()
                       ->orderBy('l.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get lead history
     */
    public function getLeadHistory($leadId)
    {
        // This would typically involve a separate lead_history table
        // For now, we'll return basic lead information
        return $this->find($leadId);
    }

    /**
     * Export leads to CSV
     */
    public function exportToCSV($filters = [])
    {
        $leads = $this->getLeadsWithDetails($filters);
        
        $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = WRITEPATH . 'uploads/' . $filename;
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp, [
            'Lead Code', 'Lead Name', 'Contact Person', 'Email', 'Phone',
            'Company', 'Source', 'Status', 'Assigned To', 'Expected Value',
            'Notes', 'Created Date'
        ]);
        
        // Write data
        foreach ($leads as $lead) {
            fputcsv($fp, [
                $lead['lead_code'],
                $lead['lead_name'],
                $lead['contact_person'],
                $lead['email'],
                $lead['phone'],
                $lead['company'],
                $lead['source'],
                $lead['status'],
                $lead['assigned_user_name'],
                $lead['expected_value'],
                $lead['notes'],
                $lead['created_at']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }
}
