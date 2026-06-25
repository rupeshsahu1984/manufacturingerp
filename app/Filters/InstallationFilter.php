<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class InstallationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if system is installed
        if (!$this->isInstalled()) {
            // Allow installer routes
            $uri = $request->getUri();
            $path = $uri->getPath();
            
            // If not accessing installer, redirect to installer
            if (strpos($path, 'installer') !== 0) {
                return redirect()->to('installer');
            }
        } else {
            // If system is installed and trying to access installer, redirect to login
            $uri = $request->getUri();
            $path = $uri->getPath();
            
            if (strpos($path, 'installer') === 0) {
                return redirect()->to('login')->with('error', 'System is already installed.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    private function isInstalled()
    {
        // Use the correct path to installed.txt
        $installedFile = dirname(dirname(__DIR__)) . '/installed.txt';
        return file_exists($installedFile);
    }
}
