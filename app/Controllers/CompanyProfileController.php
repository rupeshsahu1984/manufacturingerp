<?php

namespace App\Controllers;

use App\Models\CompanyProfile;
use CodeIgniter\HTTP\Files\UploadedFile;

class CompanyProfileController extends BaseController
{
    protected $companyProfileModel;

    public function __construct()
    {
        $this->companyProfileModel = new CompanyProfile();
    }

    public function index()
    {
        $profile = null;
        try {
            $profile = $this->companyProfileModel->getCompanyProfile();
        } catch (\Throwable $e) {
            log_message('error', 'CompanyProfileController::index: ' . $e->getMessage());
        }

        if (! $profile) {
            $profile = [
                'company_name' => '',
                'gst_number' => '',
                'registration_number' => '',
                'about_company' => '',
                'logo_path' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
            ];
        }

        $data = [
            'title' => 'PRODX - Company Profile',
            'profile' => $profile,
        ];

        return view('company_profile/index', $data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/company-profile');
        }

        $response = ['success' => false, 'message' => '', 'data' => null];

        try {
            $rules = [
                'company_name' => 'required|min_length[3]|max_length[100]',
                'gst_number' => 'required|min_length[15]|max_length[15]',
                'registration_number' => 'required|min_length[5]|max_length[50]',
                'about_company' => 'required|min_length[10]|max_length[1000]',
                'address' => 'required|min_length[10]|max_length[500]',
                'phone' => 'required|min_length[10]|max_length[15]',
                'email' => 'required|valid_email|max_length[100]',
                'website' => 'permit_empty|valid_url|max_length[100]'
            ];

            if (!$this->validate($rules)) {
                $response['message'] = 'Validation failed: ' . implode(', ', $this->validator->getErrors());
                return $this->response->setJSON($response);
            }

            $data = [
                'company_name' => $this->request->getPost('company_name'),
                'gst_number' => $this->request->getPost('gst_number'),
                'registration_number' => $this->request->getPost('registration_number'),
                'about_company' => $this->request->getPost('about_company'),
                'address' => $this->request->getPost('address'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'website' => $this->request->getPost('website')
            ];

            // Handle logo upload
            $logoFile = $this->request->getFile('logo');
            if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($logoFile->getClientMimeType(), $allowedTypes)) {
                    $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
                    return $this->response->setJSON($response);
                }

                // Validate file size (max 2MB)
                if ($logoFile->getSize() > 2 * 1024 * 1024) {
                    $response['message'] = 'File size too large. Maximum size is 2MB.';
                    return $this->response->setJSON($response);
                }

                // Get current profile to delete old logo
                $currentProfile = $this->companyProfileModel->getCompanyProfile();
                if ($currentProfile && !empty($currentProfile['logo_path'])) {
                    $this->companyProfileModel->deleteOldLogo($currentProfile['logo_path']);
                }

                // Upload new logo
                $logoPath = $this->companyProfileModel->uploadLogo($logoFile);
                if ($logoPath) {
                    $data['logo_path'] = $logoPath;
                } else {
                    $response['message'] = 'Failed to upload logo.';
                    return $this->response->setJSON($response);
                }
            }

            // Update or create company profile
            $result = $this->companyProfileModel->updateCompanyProfile($data);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Company profile updated successfully!';
                $response['data'] = $this->companyProfileModel->getCompanyProfile();
            } else {
                $response['message'] = 'Failed to update company profile.';
            }

        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }

    public function getProfile()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/company-profile');
        }

        $profile = $this->companyProfileModel->getCompanyProfile();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function deleteLogo()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/company-profile');
        }

        $response = ['success' => false, 'message' => ''];

        try {
            $profile = $this->companyProfileModel->getCompanyProfile();
            
            if ($profile && !empty($profile['logo_path'])) {
                // Delete the file
                $this->companyProfileModel->deleteOldLogo($profile['logo_path']);
                
                // Update database
                $this->companyProfileModel->update($profile['id'], ['logo_path' => null]);
                
                $response['success'] = true;
                $response['message'] = 'Logo deleted successfully!';
            } else {
                $response['message'] = 'No logo found to delete.';
            }

        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }
} 