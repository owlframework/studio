<?php
namespace Core;

// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

// Determine whether the application is running in a local development environment.
$is_local = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

if ($is_local) {
    /**
     * Local development configuration values loaded via environment variables.
     */
    define('DB_HOST', Env::get('DB_HOST', 'localhost'));
    define('DB_NAME', Env::get('DB_DATABASE'));
    define('DB_USER', Env::get('DB_USERNAME'));  
    define('DB_PASS', Env::get('DB_PASSWORD'));
    
    // Resolve base URLs from environment variables with local defaults.
    $local_domain = Env::get('SITE_DOMAIN', 'http://localhost/app/OwlStudio');
    define('UROOT', rtrim($local_domain, '/'));
    define('AROOT', rtrim($local_domain, '/') . '/admin');
    
    
} else {
    /**
     * Production environment configuration values optimized for live deployment.
     */
    $domain = Env::get('SITE_DOMAIN', 'demo.ng');

    define('DB_HOST', Env::get('DB_HOST', 'localhost'));
    define('DB_NAME', Env::get('DB_DATABASE'));
    define('DB_USER', Env::get('DB_USERNAME'));  
    define('DB_PASS', Env::get('DB_PASSWORD'));
    
    // Construct dynamic production URLs.
    define('UROOT', 'https://' . ltrim($domain, 'https://'));
    define('AROOT', 'https://' . ltrim($domain, 'https://') . '/admin');
   
}
