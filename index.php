<?php
declare(strict_types=1);

// Initialize output buffering and session handling
ob_start();
session_start();

// Define core application path constants
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (defined('DEBUG') && DEBUG) {
    // Show everything on screen for development
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // Hide everything from users on production
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}


// Load environment configuration variables from the root .env file.
require_once ROOTPATH . '/core/Env.php';
\Core\Env::load(ROOTPATH . '.env');

// Register Composer autoloader
require_once ROOTPATH . 'vendor/autoload.php';
 
// Initialize CSRF protection middleware/handler
if (function_exists('startCsrfProtection')) {
    startCsrfProtection();
}

// Track active user sessions and telemetry
$tracker = new \Core\SessionTracker();
$tracker->track();

// Bootstrap and dispatch application routing
$app = new \Core\Route();
$app->handle();


