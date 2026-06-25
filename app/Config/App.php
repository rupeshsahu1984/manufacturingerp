<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public $baseURL = 'http://localhost/manufacturingerp/';
    public $indexPage = '';
    public $uriProtocol = 'REQUEST_URI';
    public $defaultLocale = 'en';
    public $negotiateLocale = false;
    public $supportedLocales = ['en'];
    public $appTimezone = 'Asia/Kolkata';
    public $charset = 'UTF-8';
    public $forceGlobalSecureRequests = false;
    public $proxyIPs = [];
    public $CSPEnabled = false;
    public $allowedHostnames = [];
    
    // Error reporting for development
    public $displayErrors = true;
    public $log = true;
    public $logLevel = 'debug';
}
