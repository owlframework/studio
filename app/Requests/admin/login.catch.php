<?php
declare(strict_types=1);
 
    verify_post_request();
 

if (empty($_POST['email']) || empty($_POST['password'])) {
    Session::flash('error', 'All fields are required');
    Response::back();
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

$userModel = new Login();
$access = $userModel->first(['email' => $email]);

// CONSTANTS FOR THROTTLING POLICY
$maxAttempts = 5;
$lockoutDurationMinutes = 15;

// Check if user exists and is currently locked out
if ($access && !empty($access->lockout_time)) {
    $lockoutTimestamp = strtotime($access->lockout_time);
    if (time() < $lockoutTimestamp) {
        $remainingMinutes = ceil(($lockoutTimestamp - time()) / 60);
        Session::flash('error', "Account temporarily locked due to multiple failed attempts. Try again in {$remainingMinutes} minute(s).");
        Response::back();
        exit;
    } else {
        // Lockout expired, reset the counter in database
        $userModel->update($access->id, [
            'login_attempts' => 0,
            'lockout_time' => null
        ]);
    }
}

//  Perform safe password verification (timing attack mitigation)
$dummyHash = '$2y$10$abcdefghijklmnopqrstuABCDEFGHIJKLMNOPQRSTUVWXyz0123456789';
$isValidPassword = password_verify($password, $access ? $access->password : $dummyHash);

// Handle Failed Login
if (!$access || !$isValidPassword || !authorized($access->role)) {
    
    // If user actually exists in the database, increment their failed attempt count
    if ($access) {
        $newAttempts = ((int)$access->login_attempts) + 1;
        $updateData = ['login_attempts' => $newAttempts];

        // Trigger lockout if threshold is reached
        if ($newAttempts >= $maxAttempts) {
            // FIXED: Added missing multiplication operator (*)
            $updateData['lockout_time'] = date('Y-m-d H:i:s', time() + ($lockoutDurationMinutes * 60));
        }

        $userModel->update($access->id, $updateData);
    }

    Session::flash('error', 'Invalid credentials or unauthorized access.');
    Response::back();
    exit();
}

// LOGIN SUCCESS - Reset attempts and lockouts in database
$userModel->update($access->id, [
    'login_attempts' => 0,
    'lockout_time' => null
]);

session_regenerate_id(true);
Session::set(AUTH_SESSION, $access->id);

Session::flash('success', "Welcome back to the " . (defined('APPNAME') ? APPNAME : 'Owl') . " management console.");
Response::redirect('dashboard');
exit;