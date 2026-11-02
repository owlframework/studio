<?php
defined('ROOTPATH') OR die("Direct access denied.");

// Force POST method and ensure the 'email' field is present in the payload
verify_post_request('email');

// Secure the page and get the current user object
$access = user(true); 

// Security Cross-Check: Compare posted email with the actual logged-in user's email
if ($_POST['email'] !== $access->email) {
    Session::flash('error', 'Authentication mismatch.');
    Response::back();
}

// Generate New Code & Update Database
$userModel = new Register();
$newCode = $userModel->generateUniqueCode();

$userModel->update($access->id, [
    'code'       => $newCode,
    'updated_at' => date("Y-m-d H:i:s")
]);

//  Build and Dispatch the HTML Email
 
$appname = APPNAME;
$subject = "Email Verification Code";
$userEmail = $access->email;
// Render the decoupled email template
$message = render_email('email_template/verify_email', [
    'appname'       => $appname,
    'codeGenerated' => $newCode
], 'Verify Your Account');

// Send the email
send_mail($userEmail, $subject, $message);

//  Notify and Redirect Back to Verification View
Session::flash('success', 'A fresh code has been sent to ' . $userEmail);
Response::redirect('verify');