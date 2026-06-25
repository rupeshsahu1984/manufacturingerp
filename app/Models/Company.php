<?php

namespace App\Models;

use CodeIgniter\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_name', 'legal_name', 'registration_number', 'gst_number', 
        'pan_number', 'address_line1', 'address_line2', 'city', 'state', 
        'postal_code', 'country', 'phone', 'email', 'website', 'logo_path',
        'favicon_path', 'currency', 'timezone', 'date_format', 'time_format',
        'fiscal_year_start', 'fiscal_year_end', 'status', 'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'company_name' => 'required|min_length[2]|max_length[200]',
        'legal_name' => 'permit_empty|max_length[200]',
        'registration_number' => 'permit_empty|max_length[50]',
        'gst_number' => 'permit_empty|max_length[20]',
        'pan_number' => 'permit_empty|max_length[20]',
        'address_line1' => 'required|max_length[255]',
        'city' => 'required|max_length[100]',
        'state' => 'required|max_length[100]',
        'postal_code' => 'required|max_length[20]',
        'country' => 'required|max_length[100]',
        'phone' => 'required|max_length[20]',
        'email' => 'required|valid_email|max_length[100]',
        'website' => 'permit_empty|valid_url|max_length[255]',
        'currency' => 'required|max_length[10]',
        'timezone' => 'required|max_length[50]',
        'date_format' => 'required|max_length[20]',
        'time_format' => 'required|max_length[10]',
        'fiscal_year_start' => 'required|valid_date',
        'fiscal_year_end' => 'required|valid_date',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'company_name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 2 characters long',
            'max_length' => 'Company name cannot exceed 200 characters'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address'
        ],
        'phone' => [
            'required' => 'Phone number is required'
        ]
    ];

    // Get company profile
    public function getCompanyProfile()
    {
        return $this->where('status', 'active')->first();
    }

    // Update company profile
    public function updateCompanyProfile($data, $companyId = 1)
    {
        // Handle file uploads
        if (isset($data['logo']) && $data['logo']->isValid()) {
            $logoPath = $this->uploadLogo($data['logo']);
            if ($logoPath) {
                $data['logo_path'] = $logoPath;
            }
        }

        if (isset($data['favicon']) && $data['favicon']->isValid()) {
            $faviconPath = $this->uploadFavicon($data['favicon']);
            if ($faviconPath) {
                $data['favicon_path'] = $faviconPath;
            }
        }

        // Remove file objects from data array
        unset($data['logo'], $data['favicon']);

        return $this->update($companyId, $data);
    }

    // Upload company logo
    private function uploadLogo($file)
    {
        $uploadPath = FCPATH . 'uploads/company/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = 'logo_' . time() . '.' . $file->getExtension();
        $filePath = $uploadPath . $fileName;

        if ($file->move($filePath)) {
            return 'uploads/company/' . $fileName;
        }

        return false;
    }

    // Upload favicon
    private function uploadFavicon($file)
    {
        $uploadPath = FCPATH . 'uploads/company/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = 'favicon_' . time() . '.' . $file->getExtension();
        $filePath = $uploadPath . $fileName;

        if ($file->move($filePath)) {
            return 'uploads/company/' . $fileName;
        }

        return false;
    }

    // Get company settings
    public function getCompanySettings()
    {
        $company = $this->getCompanyProfile();
        
        if (!$company) {
            return $this->getDefaultSettings();
        }

        return [
            'company_name' => $company['company_name'] ?? '',
            'legal_name' => $company['legal_name'] ?? '',
            'logo_path' => $company['logo_path'] ?? '',
            'favicon_path' => $company['favicon_path'] ?? '',
            'currency' => $company['currency'] ?? 'INR',
            'timezone' => $company['timezone'] ?? 'Asia/Kolkata',
            'date_format' => $company['date_format'] ?? 'd/m/Y',
            'time_format' => $company['time_format'] ?? 'H:i',
            'fiscal_year_start' => $company['fiscal_year_start'] ?? '04-01',
            'fiscal_year_end' => $company['fiscal_year_end'] ?? '03-31',
        ];
    }

    // Get default company settings
    private function getDefaultSettings()
    {
        return [
            'company_name' => 'Your Company Name',
            'legal_name' => '',
            'logo_path' => '',
            'favicon_path' => '',
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'fiscal_year_start' => '04-01',
            'fiscal_year_end' => '03-31'
        ];
    }

    // Get available currencies
    public function getAvailableCurrencies()
    {
        return [
            'INR' => 'Indian Rupee (₹)',
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'JPY' => 'Japanese Yen (¥)',
            'AUD' => 'Australian Dollar (A$)',
            'CAD' => 'Canadian Dollar (C$)',
            'CHF' => 'Swiss Franc (CHF)',
            'CNY' => 'Chinese Yuan (¥)',
            'SGD' => 'Singapore Dollar (S$)'
        ];
    }

    // Get available timezones
    public function getAvailableTimezones()
    {
        return [
            'Asia/Kolkata' => 'India Standard Time (IST)',
            'Asia/Dubai' => 'Gulf Standard Time (GST)',
            'Asia/Singapore' => 'Singapore Time (SGT)',
            'Asia/Tokyo' => 'Japan Standard Time (JST)',
            'America/New_York' => 'Eastern Time (ET)',
            'America/Chicago' => 'Central Time (CT)',
            'America/Denver' => 'Mountain Time (MT)',
            'America/Los_Angeles' => 'Pacific Time (PT)',
            'Europe/London' => 'Greenwich Mean Time (GMT)',
            'Europe/Paris' => 'Central European Time (CET)'
        ];
    }

    // Get available date formats
    public function getAvailableDateFormats()
    {
        return [
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd-m-Y' => 'DD-MM-YYYY',
            'm-d-Y' => 'MM-DD-YYYY',
            'd.m.Y' => 'DD.MM.YYYY',
            'm.d.Y' => 'MM.DD.YYYY'
        ];
    }

    // Get available time formats
    public function getAvailableTimeFormats()
    {
        return [
            'H:i' => '24-hour (HH:MM)',
            'h:i A' => '12-hour (HH:MM AM/PM)',
            'H:i:s' => '24-hour with seconds (HH:MM:SS)',
            'h:i:s A' => '12-hour with seconds (HH:MM:SS AM/PM)'
        ];
    }

    // Check if company profile exists
    public function companyExists()
    {
        return $this->countAll() > 0;
    }

    // Create initial company profile
    public function createInitialProfile($data)
    {
        $data['status'] = 'active';
        return $this->insert($data);
    }
}
