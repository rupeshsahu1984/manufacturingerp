<?php

// Check if system is installed
if (!file_exists(__DIR__ . '/../installed.txt')) {
    // If not installed, redirect to installer
    header('Location: installer');
    exit;
}

// Check if we can access the CodeIgniter application
if (!file_exists(__DIR__ . '/../app/Config/App.php')) {
    die('CodeIgniter application not found. Please check your installation.');
}

// Define ENVIRONMENT constant if not already defined
if (!defined('ENVIRONMENT')) {
    // Check if .env file exists and load it
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        if (preg_match('/CI_ENVIRONMENT\s*=\s*(\w+)/', $envContent, $matches)) {
            define('ENVIRONMENT', $matches[1]);
        } else {
            define('ENVIRONMENT', 'development');
        }
    } else {
        // Fallback to development environment
        define('ENVIRONMENT', 'development');
    }
}

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, and loads our environment file.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
require realpath(FCPATH . '../app/Config/Paths.php') ?: FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

/*
 * ---------------------------------------------------------------
 * GRAB OUR CODEIGNITER INSTANCE
 * ---------------------------------------------------------------
 *
 * The CodeIgniter class contains the core functionality to make
 * the application run, and does all the dirty work to get
 * the pieces all working together.
 */

$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is set up, it's time to actually fire
 * up the engines and make this app do its thang.
 */

$app->run();

// Save the log file
// $app->finalize();
