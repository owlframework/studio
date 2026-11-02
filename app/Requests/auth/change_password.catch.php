<?php
defined('ROOTPATH') OR die("Direct access denied.");
 

// Enforce strict POST request discipline
verify_post_request();

// Get logged-in user securely
$access = user(true); 

$rules = [
    'current_password' => 'required|min:7',
    'new_password'    => 'required|min:7',
    'confirm_password'    => 'required|min:7'
];
    
// Validate input using the global Validator
if (!Validator::validate($_POST, $rules)) {
    Session::flash('error', 'Please Check Your Input, Minimum Character is 7');
    Response::back();
}

// Ensure input fields are present
$old_pass = $_POST['current_password'] ?? '';
$new_pass = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (empty($old_pass) || empty($new_pass) || empty($confirm)) {
    Session::flash('error', 'All password fields are required.');
    Response::back();
}

// Verify the "Current Password" is correct
if (!password_verify($old_pass, $access->password)) {
    Session::flash('error', 'The current password you entered is incorrect.');
    Response::back();
}

//  Check if New and Confirm match
if ($new_pass !== $confirm) {
    Session::flash('error', 'New passwords do not match.');
    Response::back();
}

// Ensure they aren't reusing the old password
if (password_verify($new_pass, $access->password)) {
    Session::flash('error', 'New password must be different from your current one.');
    Response::back();
}

// Hash and Update the Database
$userModel = new User();
$userModel->update($access->id, [
    'password' => password_hash($new_pass, PASSWORD_DEFAULT)
]);

// Send Email Notification
$appname = APPNAME;
$userEmail = $access->email;
$link = url('login');
$username = $access->username ?? 'User';
$subject = "Your Password Has Successfully Been Changed.";

// Render the decoupled email template
$message = render_email('email_template/change_password', [
    'appname'       => $appname,
    'username'      => $username,
    'link'          => $link,
], 'Password Has Been Changed');

// Send the email
send_mail($userEmail, $subject, $message);
 
//sho success flash and redirect
Session::flash('success', 'Password updated successfully!');
Response::redirect('change_password');

