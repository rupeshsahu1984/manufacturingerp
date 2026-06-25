<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Redirect to the main dashboard
        return redirect()->to('/dashboard');
    }
}
