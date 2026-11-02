<?php
defined('ROOTPATH') OR die("Direct access denied.");
 
// Enforce strict POST request and ensure 'email' field exists
verify_post_request('email');

$email = trim($_POST['email']);

//  Look up the user by email
$userModel = new User();
$access = $userModel->first(['email' => $email]);

//  Process token generation and email only if the user exists
if ($access) {
    $resetModel = new PasswordReset();
    $token = bin2hex(random_bytes(32));

    // Insert the password reset record
    $resetModel->insert([
        'email'      => $access->email,
        'token'      => $token,
        'used'       => 0,
        'created_at' => date("Y-m-d H:i:s")
    ]);
    
    // Build and Send the HTML Email
    $appname = APPNAME;
    $userEmail = $access->email;
    $username = $access->username;
    $link = url('reset_password?token=' . $token);
    $subject = "Reset Password - $appname";

    // Render the decoupled email template
    $message = render_email('email_template/forget_password', [
        'appname'       => $appname,
        'username'       => $username,
        'link'          => $link
    ], 'Reset Password');

    // Send the email
    send_mail($userEmail, $subject, $message);
}

//Security: Always show generic success to prevent email fishing attacks
Session::flash('success', 'If an account exists with that email, a reset link has been sent.');
Response::redirect('forget_password');