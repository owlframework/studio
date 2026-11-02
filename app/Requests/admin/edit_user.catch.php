<?php
declare(strict_types=1);
 
    verify_post_request();
 

$userModel = new User();
$access = admin(true);

// Global Permission Check
if (!$access || !function_exists('authorized') || !authorized($access->role)) {
    Session::flash('error', 'Unauthorized access.');
    Response::redirect('admin/dashboard');
    exit;
}

// Collect the "Double-Key" identifiers
$idFromForm = $_POST['id'] ?? null;
$emailFromForm = $_POST['email'] ?? null;

if (!$idFromForm || !$emailFromForm) {
    Session::flash('error', 'Missing critical identification data.');
    Response::back();
    exit;
}

// THE SECURITY MATCH (Double-Key IDOR protection)
$user = $userModel->first([
    'id'    => (int)$idFromForm, 
    'email' => trim($emailFromForm)
]);

if (!$user) {
    Session::flash('error', 'Security Alert: Data integrity check failed.');
    Response::back();
    exit;
}

// Prepare and sanitize data
$data = [
    'username'   => trim($_POST['username'] ?? $user->username),
    'phone'      => trim($_POST['phone'] ?? $user->phone),
    'status'     => trim($_POST['status'] ?? $user->status),
    'updated_at' => date("Y-m-d H:i:s")
];

//Execute Secure Update and capture the result
$updated = $userModel->update($user->id, $data);

if ($updated) {
    Session::flash('success', 'User record updated successfully.');
} else {
    Session::flash('error', 'Failed to update user record in database.');
}

Response::redirect('edit_user?id=' . $user->id);
exit;