<?php
defined('ROOTPATH') OR die("Direct access denied.");

// Enforce strict POST request and ensure 'token' exists
verify_post_request('token');

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Validate empty fields
if (empty($token) || empty($password) || empty($confirm)) {
    Session::flash('error', 'All fields are required.');
    Response::back();
}

//Check if passwords match
if ($password !== $confirm) {
    Session::flash('error', 'Passwords do not match.');
    Response::back();
}

// Verify the token exists and has not been used
$resetModel = new PasswordReset();
$request = $resetModel->first(['token' => $token, 'used' => 0]);

if (!$request) {
    Session::flash('error', 'Invalid or expired password reset link.');
    Response::redirect('forget_password');
}

// Look up the user associated with this token request
$userModel = new User();
$access = $userModel->first(['email' => $request->email]);

if (!$access) {
    Session::flash('error', 'Associated user account not found.');
    Response::redirect('forget_password');
}

// Ensure they are not reusing their current password
if (password_verify($password, $access->password)) {
    Session::flash('error', 'You cannot use your current password. Please choose a new one.');
    Response::back();
}

//  Hash the new password and update the user record
$new_password = password_hash($password, PASSWORD_DEFAULT);

$userModel->update($access->id, [
    'password' => $new_password
]);

// Invalidate the reset token so it cannot be reused
$resetModel->update($request->id, ['used' => 1]);

//  Build and Send Success Email Notification
$appname = APPNAME;
$link = url('login');
$userEmail = $access->email;
$subject = "Password Successfully Reset.";

    // Render the decoupled email template
    $message = render_email('email_template/reset_password', [
        'appname'       => $appname,
        'link'          => $link
    ], 'Password Successfully Reset');

    // Send the email
    send_mail($userEmail, $subject, $message);

// Success flash and redirect to login
Session::flash('success', 'Your password has been reset! Please login.');
Response::redirect('login');