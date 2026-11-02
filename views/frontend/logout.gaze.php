<?php
defined('ROOTPATH') OR die("Direct access denied.");

/**
 * Owl Framework - Logout Handler
 * Supports both form submissions (POST) and regular anchor links (GET).
 */

$method = $_SERVER['REQUEST_METHOD'];

// Verify security if it's a POST, otherwise block unauthorized methods
if ($method === 'POST') {
    verify_post_request(); 
} elseif ($method !== 'GET') {
    // Block any weird methods like PUT, DELETE, etc.
    Response::redirect('login');
}

// Completely wipe out the session and all stored data (runs for both GET and POST)
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

// Redirect immediately to the login page
Response::redirect('login');