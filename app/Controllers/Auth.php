<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            // The login form sends `username` and `password`
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Find user by username (and make sure they are active)
            $user = $this->userModel
                ->where('username', $username)
                ->where('status', 'active')
                ->first();

            if ($user && password_verify($password, $user['password'])) {
                $session = session();
                // Store only the fields you really need in session
                $session->set([
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'user_email' => $user['email'],
                    'user_role'  => $user['role'],
                    'logged_in'  => true
                ]);

                return redirect()->to('dashboard');
            } else {
                session()->setFlashdata('error', 'Invalid email or password');
                return redirect()->back();
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        
        session()->setFlashdata('success', 'You have been successfully logged out');
        return redirect()->to('login');
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return redirect()->to('login');
    }
} 
