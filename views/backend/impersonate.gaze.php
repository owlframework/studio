<?php
declare(strict_types=1);
require 'feather.php';

// 1. Ensure current user is an authorized admin
$currentAdmin = user();
if (!$currentAdmin || !function_exists('isAdmin') || !isAdmin($currentAdmin->role)) {
    Session::flash('error', 'Unauthorized access.');
    Response::redirect('dashboard');
    exit;
}

// 2. Get and validate the Target User ID from the URL
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    Session::flash('error', 'Invalid user target specified.');
    Response::redirect('admin/dashboard');
    exit;
}

$userModel = new User();
$targetUser = $userModel->first(['id' => (int)$id]);

if (!$targetUser) {
    Session::flash('error', 'Target user not found.');
    Response::redirect('admin/users');
    exit;
}

// 3. PRIVILEGE ESCALATION CHECK: Prevent admins from impersonating Super Admins
if ($targetUser->role === 'super_admin' && $currentAdmin->role !== 'super_admin') {
    Session::flash('error', 'Security violation: Cannot impersonate a Super Administrator.');
    Response::redirect('admin/users');
    exit;
}

// Prevent an admin from impersonating themselves
if ((int)$targetUser->id === (int)$currentAdmin->id) {
    Response::redirect('dashboard');
    exit;
}

// 4. Store the Admin ID securely so we can "come back" later
Session::set('original_admin_id', $currentAdmin->id);

// 5. Regenerate session ID and swap session to target user
session_regenerate_id(true);
Session::set(AUTH_SESSION, $targetUser->id);

// 6. Flash notice and redirect to frontend dashboard
Session::flash('success', "You are now viewing as {$targetUser->username}.");
Response::redirect('dashboard');
exit;