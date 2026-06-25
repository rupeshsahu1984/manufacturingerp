<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_name',
        'gst_number',
        'registration_number',
        'about_company',
        'logo_path',
        'address',
        'phone',
        'email',
        'website',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'company_name' => 'required|min_length[3]|max_length[100]',
        'gst_number' => 'required|min_length[15]|max_length[15]',
        'registration_number' => 'required|min_length[5]|max_length[50]',
        'about_company' => 'required|min_length[10]|max_length[1000]',
        'address' => 'required|min_length[10]|max_length[500]',
        'phone' => 'required|min_length[10]|max_length[15]',
        'email' => 'required|valid_email|max_length[100]',
        'website' => 'permit_empty|valid_url|max_length[100]'
    ];

    protected $validationMessages = [
        'company_name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 3 characters long',
            'max_length' => 'Company name cannot exceed 100 characters'
        ],
        'gst_number' => [
            'required' => 'GST number is required',
            'min_length' => 'GST number must be 15 characters long',
            'max_length' => 'GST number must be 15 characters long'
        ],
        'registration_number' => [
            'required' => 'Registration number is required',
            'min_length' => 'Registration number must be at least 5 characters long',
            'max_length' => 'Registration number cannot exceed 50 characters'
        ],
        'about_company' => [
            'required' => 'About company information is required',
            'min_length' => 'About company must be at least 10 characters long',
            'max_length' => 'About company cannot exceed 1000 characters'
        ],
        'address' => [
            'required' => 'Company address is required',
            'min_length' => 'Address must be at least 10 characters long',
            'max_length' => 'Address cannot exceed 500 characters'
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'min_length' => 'Phone number must be at least 10 characters long',
            'max_length' => 'Phone number cannot exceed 15 characters'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'max_length' => 'Email cannot exceed 100 characters'
        ],
        'website' => [
            'valid_url' => 'Please enter a valid website URL',
            'max_length' => 'Website URL cannot exceed 100 characters'
        ]
    ];

    // Get company profile
    public function getCompanyProfile()
    {
        return $this->first();
    }

    // Update or create company profile
    public function updateCompanyProfile($data)
    {
        $profile = $this->first();
        
        if ($profile) {
            // Update existing profile
            return $this->update($profile['id'], $data);
        } else {
            // Create new profile
            return $this->insert($data);
        }
    }

    // Upload logo
    public function uploadLogo($file)
    {
        $uploadPath = FCPATH . 'uploads/logos/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $filename = 'company_logo_' . time() . '.' . $extension;
        $filepath = $uploadPath . $filename;

        // Move uploaded file
        if ($file->move($uploadPath, $filename)) {
            return 'uploads/logos/' . $filename;
        }

        return false;
    }

    // Delete old logo
    public function deleteOldLogo($logoPath)
    {
        if ($logoPath && file_exists(FCPATH . $logoPath)) {
            unlink(FCPATH . $logoPath);
        }
    }
} 