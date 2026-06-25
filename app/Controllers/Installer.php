<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ConnectionInterface;

class Installer extends Controller
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    // Check if system is already installed
    public function index()
    {
        // Check if system is already installed
        if ($this->isInstalled()) {
            return redirect()->to('login')->with('error', 'System is already installed.');
        }

        $data = [
            'title' => 'Manufacturing ERP - Installation',
            'step' => 1,
            'total_steps' => 4
        ];

        return view('installer/welcome', $data);
    }

    // Step 1: Database Configuration
    public function database()
    {
        if ($this->isInstalled()) {
            return redirect()->to('login');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->validateDatabase();
        }

        $data = [
            'title' => 'Database Configuration',
            'step' => 1,
            'total_steps' => 4,
            'error' => $this->session->getFlashdata('error'),
            'success' => $this->session->getFlashdata('success')
        ];

        return view('installer/database', $data);
    }

    // Step 2: System Installation
    public function install()
    {
        if ($this->isInstalled()) {
            return redirect()->to('login');
        }

        $data = [
            'title' => 'System Installation',
            'step' => 2,
            'total_steps' => 4
        ];

        return view('installer/install', $data);
    }

    // Step 3: Company Setup
    public function company()
    {
        if ($this->isInstalled()) {
            return redirect()->to('login');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->saveCompanySetup();
        }

        $data = [
            'title' => 'Company Setup',
            'step' => 3,
            'total_steps' => 4,
            'error' => $this->session->getFlashdata('error'),
            'success' => $this->session->getFlashdata('success')
        ];

        return view('installer/company', $data);
    }

    // Step 4: Super Admin Setup
    public function admin()
    {
        if ($this->isInstalled()) {
            return redirect()->to('login');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->saveAdminSetup();
        }

        $data = [
            'title' => 'Super Admin Setup',
            'step' => 4,
            'total_steps' => 4,
            'error' => $this->session->getFlashdata('error'),
            'success' => $this->session->getFlashdata('success')
        ];

        return view('installer/admin', $data);
    }

    // Complete Installation
    public function complete()
    {
        if ($this->isInstalled()) {
            return redirect()->to('login');
        }

        // Mark system as installed
        $this->markAsInstalled();

        $data = [
            'title' => 'Installation Complete',
            'step' => 4,
            'total_steps' => 4
        ];

        return view('installer/complete', $data);
    }

    // Validate database connection
    private function validateDatabase()
    {
        $hostname = $this->request->getPost('hostname');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $database = $this->request->getPost('database');

        // Test database connection
        try {
            $testDb = \Config\Database::connect([
                'hostname' => $hostname,
                'username' => $username,
                'password' => $password,
                'database' => $database,
                'DBDriver' => 'MySQLi',
                'DBPrefix' => '',
                'port' => 3306
            ]);

            // Test if we can connect
            $testDb->connect();

            // Save database config to session for later use
            $this->session->set([
                'db_hostname' => $hostname,
                'db_username' => $username,
                'db_password' => $password,
                'db_database' => $database
            ]);

            return redirect()->to('installer/install')->with('success', 'Database connection successful!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database connection failed: ' . $e->getMessage());
        }
    }

    // Save company setup
    private function saveCompanySetup()
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email' => 'required|valid_email',
            'phone' => 'required|max_length[20]',
            'address' => 'required|max_length[255]',
            'city' => 'required|max_length[100]',
            'state' => 'required|max_length[100]',
            'postal_code' => 'required|max_length[20]',
            'country' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input and try again.');
        }

        // Handle logo upload
        $logoPath = '';
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/company/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $fileName = 'logo_' . time() . '.' . $logo->getExtension();
            if ($logo->move($uploadPath, $fileName)) {
                $logoPath = 'uploads/company/' . $fileName;
            }
        }

        // Save company info to session
        $this->session->set([
            'company_name' => $this->request->getPost('company_name'),
            'company_email' => $this->request->getPost('email'),
            'company_phone' => $this->request->getPost('phone'),
            'company_address' => $this->request->getPost('address'),
            'company_city' => $this->request->getPost('city'),
            'company_state' => $this->request->getPost('state'),
            'company_postal_code' => $this->request->getPost('postal_code'),
            'company_country' => $this->request->getPost('country'),
            'company_logo' => $logoPath
        ]);

        return redirect()->to('installer/admin')->with('success', 'Company information saved successfully!');
    }

    // Save admin setup
    private function saveAdminSetup()
    {
        $rules = [
            'admin_name' => 'required|min_length[3]|max_length[100]',
            'admin_email' => 'required|valid_email',
            'admin_username' => 'required|min_length[3]|max_length[50]',
            'admin_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[admin_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input and try again.');
        }

        // Save admin info to session
        $this->session->set([
            'admin_name' => $this->request->getPost('admin_name'),
            'admin_email' => $this->request->getPost('admin_email'),
            'admin_username' => $this->request->getPost('admin_username'),
            'admin_password' => $this->request->getPost('admin_password')
        ]);

        return redirect()->to('installer/complete')->with('success', 'Super Admin account created successfully!');
    }

    // Check if system is installed
    private function isInstalled()
    {
        return file_exists(FCPATH . 'installed.txt');
    }

    // Mark system as installed
    private function markAsInstalled()
    {
        // Create installed.txt file
        file_put_contents(FCPATH . 'installed.txt', date('Y-m-d H:i:s'));

        // Update database configuration
        $this->updateDatabaseConfig();

        // Create database tables
        $this->createDatabaseTables();

        // Insert company data
        $this->insertCompanyData();

        // Insert admin user
        $this->insertAdminUser();

        // Clear session data
        $this->session->destroy();
    }

    // Update database configuration
    private function updateDatabaseConfig()
    {
        $dbConfig = [
            'hostname' => $this->session->get('db_hostname'),
            'username' => $this->session->get('db_username'),
            'password' => $this->session->get('db_password'),
            'database' => $this->session->get('db_database'),
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'port' => 3306
        ];

        // Update the database configuration file
        $configPath = APPPATH . 'Config/Database.php';
        $configContent = file_get_contents($configPath);
        
        // Replace the default configuration
        $configContent = preg_replace(
            "/'hostname' => 'localhost'/",
            "'hostname' => '{$dbConfig['hostname']}'",
            $configContent
        );
        $configContent = preg_replace(
            "/'username' => 'root'/",
            "'username' => '{$dbConfig['username']}'",
            $configContent
        );
        $configContent = preg_replace(
            "/'password' => ''/",
            "'password' => '{$dbConfig['password']}'",
            $configContent
        );
        $configContent = preg_replace(
            "/'database' => 'ci4'/",
            "'database' => '{$dbConfig['database']}'",
            $configContent
        );

        file_put_contents($configPath, $configContent);
    }

    // Create database tables
    private function createDatabaseTables()
    {
        $db = \Config\Database::connect();
        
        // Read and execute the database schema
        $schemaPath = FCPATH . 'database_schema.sql';
        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            $db->query($sql);
        }
    }

    // Insert company data
    private function insertCompanyData()
    {
        $db = \Config\Database::connect();
        
        $companyData = [
            'company_name' => $this->session->get('company_name'),
            'email' => $this->session->get('company_email'),
            'phone' => $this->session->get('company_phone'),
            'address_line1' => $this->session->get('company_address'),
            'city' => $this->session->get('company_city'),
            'state' => $this->session->get('company_state'),
            'postal_code' => $this->session->get('company_postal_code'),
            'country' => $this->session->get('company_country'),
            'logo_path' => $this->session->get('company_logo'),
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'fiscal_year_start' => '04-01',
            'fiscal_year_end' => '03-31',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db->table('companies')->insert($companyData);
    }

    // Insert admin user
    private function insertAdminUser()
    {
        $db = \Config\Database::connect();
        
        $adminData = [
            'username' => $this->session->get('admin_username'),
            'email' => $this->session->get('admin_email'),
            'password' => password_hash($this->session->get('admin_password'), PASSWORD_DEFAULT),
            'full_name' => $this->session->get('admin_name'),
            'role' => 'super_admin',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db->table('users')->insert($adminData);
    }

    // AJAX: Test database connection
    public function testDatabase()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $hostname = $this->request->getPost('hostname');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $database = $this->request->getPost('database');

        try {
            $testDb = \Config\Database::connect([
                'hostname' => $hostname,
                'username' => $username,
                'password' => $password,
                'database' => $database,
                'DBDriver' => 'MySQLi',
                'DBPrefix' => '',
                'port' => 3306
            ]);

            $testDb->connect();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database connection successful!'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]);
        }
    }
}
