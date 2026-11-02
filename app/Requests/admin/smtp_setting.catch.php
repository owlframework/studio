<?php
declare(strict_types=1);
 
    verify_post_request();
 
// Basic check for required fields
if (empty($_POST['id']) || empty($_POST['host']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['port']) || empty($_POST['from_email'])) {
    Session::flash('error', 'All required SMTP fields must be filled.');
    Response::redirect('smtp_setting');
    exit;
}

// Validate email format
if (!filter_var($_POST['from_email'], FILTER_VALIDATE_EMAIL)) {
    Session::flash('error', 'Please provide a valid sender email address.');
    Response::redirect('smtp_setting');
    exit;
}

$smtpId = (int)$_POST['id'];

// Initialize Model
$smtp = new Mail();

// Verify the settings row exists
$settings = $smtp->first(['id' => $smtpId]); 

if (!$settings) {
    Session::flash('error', 'SMTP settings row not found in database.');
    Response::redirect('smtp_setting');
    exit;
}

// DEFINE THE DATA TO UPDATE (Sanitized / Trimmed)
$data = [
    'host'       => trim($_POST['host']),
    'username'   => trim($_POST['username']),
    'password'   => $_POST['password'], // Note: Keep raw password so hashing doesn't break SMTP auth
    'port'       => (int)$_POST['port'],
    'from_name'  => trim($_POST['from_name'] ?? ''),
    'from_email' => trim($_POST['from_email']),
    'replyto'    => trim($_POST['replyto'] ?? '')
];

// Run the update and check  
$updated = $smtp->update($settings->id, $data);

if ($updated) {
    Session::flash('success', 'SMTP settings updated successfully!');
} else {
    Session::flash('error', 'Something went wrong during the update.');
}

Response::redirect('smtp_setting');
exit;