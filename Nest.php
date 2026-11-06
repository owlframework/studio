<?php
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");
// Configure application debugging mode flag.
define('DEBUG', true);

// Configure global application runtime timezone.
date_default_timezone_set('Africa/Lagos');

// Define core application metadata and identification constants.
define('APPNAME', "Owl Php");

// Configure authentication session key identifier.
define('AUTH_SESSION', 'USER_ID');

// Configure secure HTTP response headers for enhanced protection.
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Generate cryptographic nonce for Content Security Policy execution.
$nonce = function_exists('get_nonce') ? get_nonce() : '';

// Configure and apply Content Security Policy (CSP) headers.
header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'nonce-$nonce' https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " . 
    "style-src 'self' 'nonce-$nonce' https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " . 
    "font-src 'self' data: https://fonts.gstatic.com https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " . 
    "img-src 'self' data: blob:; " .
    "connect-src 'self' blob: https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;"
);

// Require core framework classes and register global class aliases.
require_once ROOTPATH . 'core/Model.php';
require_once ROOTPATH . 'core/Session.php';
require_once ROOTPATH . 'core/Response.php';
require_once ROOTPATH . 'core/Validator.php';
require_once ROOTPATH . 'core/SessionTracker.php';

if (!class_exists('Model', false))    class_alias('Core\Model', 'Model');
if (!class_exists('Session', false))  class_alias('Core\Session', 'Session');
if (!class_exists('Response', false)) class_alias('Core\Response', 'Response');
if (!class_exists('Validator', false))  class_alias('Core\Validator', 'Validator');
if (!class_exists('SessionTracker', false))  class_alias('Core\SessionTracker', 'SessionTracker');

// Dynamically load application model files and register global class aliases.
$appModels = [
    'Login',
    'Mail',
    'Password',
    'PasswordReset',
    'Register',
    'User'   
    
];

foreach ($appModels as $modelName) {
    $modelFile = ROOTPATH . 'app/Models/' . $modelName . '.php';
    
    if (file_exists($modelFile)) {
        require_once $modelFile;
        
        $fullyQualified = '\\App\\Models\\' . $modelName;
        if (class_exists($fullyQualified, false) && !class_exists($modelName, false)) {
            class_alias($fullyQualified, $modelName);
        }
    }
}

if (!function_exists('email_layout')) {
    function email_layout($title, $content) {
    $appname = defined('APPNAME') ? APPNAME : 'Owl PHP';
    
    // Global email styling variables (easy for developers to rebrand)
   
    $brandColor = "#f3a81a"; 
    $fontFamily = "Arial, sans-serif";
    $year = date('Y');

    return "
    <div style='font-family: {$fontFamily}; background-color: #f4f4f4; padding: 20px; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
            
            <!-- Master Brand Header -->
            <div style='background-color: {$brandColor}; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;'>" . esc($appname) . "</h1>
            </div>

            <!-- Dynamic Body Content Injection -->
            <div style='padding: 40px; text-align: left; line-height: 1.6; color: #333333;'>
                <h2 style='color: {$brandColor}; margin-top: 0; text-align: center;'>" . esc($title) . "</h2>
                
                " . $content . "
                
            </div>

            <!-- Master Footer -->
            <div style='background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999;'>
                <p style='margin: 5px 0;'>&copy; " . $year . " " . esc($appname) . ". All rights reserved.</p>
            </div>
        </div>
    </div>
    ";
    }
}