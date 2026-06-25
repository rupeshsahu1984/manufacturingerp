<?php

namespace App\Controllers;

class HelpController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Help & Documentation',
            'page_title' => 'Help & Documentation',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Help', 'url' => '']
            ]
        ];
        
        return view('help/index', $data);
    }
    
    public function support()
    {
        $data = [
            'title' => 'Support',
            'page_title' => 'Support',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Help', 'url' => base_url('help')],
                ['title' => 'Support', 'url' => '']
            ]
        ];
        
        return view('help/support', $data);
    }
    
    public function documentation()
    {
        $data = [
            'title' => 'Documentation',
            'page_title' => 'Documentation',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Help', 'url' => base_url('help')],
                ['title' => 'Documentation', 'url' => '']
            ]
        ];
        
        return view('help/documentation', $data);
    }
    
    public function faq()
    {
        $data = [
            'title' => 'FAQ',
            'page_title' => 'Frequently Asked Questions',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Help', 'url' => base_url('help')],
                ['title' => 'FAQ', 'url' => '']
            ]
        ];
        
        return view('help/faq', $data);
    }
    
    public function contact()
    {
        $data = [
            'title' => 'Contact Support',
            'page_title' => 'Contact Support',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Help', 'url' => base_url('help')],
                ['title' => 'Contact', 'url' => '']
            ]
        ];
        
        return view('help/contact', $data);
    }
}
