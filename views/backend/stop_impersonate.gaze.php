<?php
declare(strict_types=1);
 

// 1. Check if an active impersonation session exists
if (Session::has('original_admin_id')) {
    $adminId = Session::get('original_admin_id');
    
    // Clean up the temporary impersonation tracker
    Session::unset('original_admin_id');
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Put the Admin back in charge
    Session::set(AUTH_SESSION, $adminId);
    
    Session::flash('success', 'Returned to your administrator account.');
    Response::redirect('dashboard');
    exit;
}

// Fallback if someone hits this route accidentally without impersonating
Response::redirect('dashboard');
exit;