<?php
defined('ROOTPATH') OR die("Direct access denied.");

verify_post_request();
// Connect to the User model (globally available via Nest.php)
$userModel = new Register();

// Set the validation rules (fixed 'requried' typos)
$rules = [
    'username' => 'required|min:4',
    'email'    => 'required|email',
    'phone'    => 'required|min:8',
    'password' => 'required|min:5'
];
    
// Validate input using the global Validator
if (!Validator::validate($_POST, $rules)) {
    Session::flash('error', 'Please Check Your Input');
    Response::back();
}
    
// Bot Check
$customCheck = botCheck($_POST['email'], $_POST['username']);

if ($customCheck !== true) {
    Session::flash('error', $customCheck);
    Response::back();
}
    


// Check if email already exists in the database
if ($userModel->first(['email' => $_POST['email']])) {
    Session::flash('error', 'Email Already Exist');
    Response::back();
}

// Check if username already exists in the database
if ($userModel->first(['username' => $_POST['username']])) {
    Session::flash('error', 'Username Already Exsit');
    Response::back();
}

// Secure the password
$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Generate verification code
$codeGenerated = $userModel->generateUniqueCode();
$_POST['code'] = $codeGenerated;

$userEmail = $_POST['email'];
 
// ... your existing registration database insertion logic ...

$appname = APPNAME;
$subject = "Email Verification Code";

// Render the decoupled email template
$message = render_email('email_template/verify_email', [
    'appname'       => $appname,
    'codeGenerated' => $codeGenerated
], 'Verify Your Account');

// Send the email
send_mail($userEmail, $subject, $message);
 
// Insert data into database
$userModel->insert($_POST);

// Retrieve the newly created user object
$access = $userModel->first(['email' => $_POST['email']]);

// Validate and start session
if ($access) {
    Session::set(AUTH_SESSION, $access->id);
    
    Session::flash('success', 'Account Created! Check your email for your code.');
    Response::redirect('verify');
} else {
    Session::flash('error', 'Something went wrong during registration.');
    Response::back();
}