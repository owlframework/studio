<?php
defined('ROOTPATH') OR die("Direct access denied.");

// Force POST method and ensure 'code' exists in the submission
verify_post_request('code');

// Ensure the user is authenticated (they got here right after registering)
$userId = Session::get(AUTH_SESSION);
if (!$userId) {
    Session::flash('error', 'Session expired. Please log in again.');
    Response::redirect('login');
}

//  Connect to the User model and fetch the current user
$userModel = new User();
$user = $userModel->first(['id' => $userId]);

if (!$user) {
    Session::flash('error', 'User account not found.');
    Response::redirect('login');
}

//Check if already verified
if ($user->status === 'active') {
    Session::flash('success', 'Your account is already active.');
    Response::redirect('dashboard');
}

// Validate the entered code against the logged-in user's record
$enteredCode = trim($_POST['code']);

if (empty($enteredCode) || $user->code !== $enteredCode) {
    Session::flash('error', 'Invalid verification code.');
    Response::back();
}

// Check Code Expiration (10-minute window)
$expiryLimit = 10 * 60;
$createdTime = strtotime($user->updated_at);
$currentTime = time();

if (($currentTime - $createdTime) > $expiryLimit) {
    Session::flash('error', 'The code has expired. Please request a new code.');
    Response::back();
}

// Activate the user and clear the one-time code
$data = [
    'status'             => 'active',
    'email_verified_at'  => date("Y-m-d H:i:s"),
    'code'               => null
];

$userModel->update($user->id, $data);

// Success response
Session::flash('success', 'Verification Successful! Welcome aboard.');
Response::redirect('dashboard');