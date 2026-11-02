<?php
declare(strict_types=1);
verify_post_request();
$userModel = new Login();

// GUEST-ONLY CHECK: Prevent logged-in users from accessing the login page
if ($access = user()) {
    if (empty($access->email_verified_at)) {
        Response::redirect('verify');
    } else {
        if (function_exists('authorized') && authorized($access->role)) {
            Response::redirect('admin/dashboard');
        } else {
            Response::redirect('dashboard');
        }
    }
    exit;
}

//CHECK IF FORM FIELDS ARE EMPTY FIRST 
if (empty($_POST['username']) || empty($_POST['password'])) {
    Session::flash('error', 'All fields are required.');
    Response::back();
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// FIND THE USER IN THE DATABASE FIRST (Needed for DB throttling)
$user = $userModel->first(['username' => $username]);

// THROTTLING CONFIGURATION
$maxAttempts = 5;
$lockoutDurationMinutes = 5; // 5 minutes

// CHECK IF USER IS CURRENTLY LOCKED OUT IN THE DATABASE
if ($user && !empty($user->lockout_time)) {
    $lockoutTimestamp = strtotime($user->lockout_time);
    if (time() < $lockoutTimestamp) {
        $remainingMinutes = ceil(($lockoutTimestamp - time()) / 60);
        Session::flash('error', "Account temporarily locked due to multiple failed attempts. Try again in {$remainingMinutes} minute(s).");
        Response::back();
        exit;
    } else {
        // Lockout expired, reset the counter in the database
        $userModel->update($user->id, [
            'login_attempts' => 0,
            'lockout_time' => null
        ]);
    }
}

// TIMING ATTACK PREVENTION & PASSWORD VERIFICATION
$dummyHash = '$2y$10$abcdefghijklmnopqrstuABCDEFGHIJKLMNOPQRSTUVWXyz0123456789';
$passwordToCheck = $user ? $user->password : $dummyHash;
$isPasswordValid = password_verify($password, $passwordToCheck);

// HANDLE FAILED LOGIN
if (!$user || !$isPasswordValid) {
    
    // If user actually exists in the database, increment their failed attempt count
    if ($user) {
        $newAttempts = ((int)$user->login_attempts) + 1;
        $updateData = ['login_attempts' => $newAttempts];

        // Trigger lockout if threshold is reached
        if ($newAttempts >= $maxAttempts) {
            $updateData['lockout_time'] = date('Y-m-d H:i:s', time() + ($lockoutDurationMinutes * 60));
        }

        $userModel->update($user->id, $updateData);
    }

    Session::flash('error', 'Invalid Username or password.');
    Response::back();
    exit;
}

//LOGIN SUCCESSFUL - Reset attempts and lockouts in the database
$userModel->update($user->id, [
    'login_attempts' => 0,
    'lockout_time' => null
]);

// Regenerate session ID to prevent session fixation attacks
if (function_exists('session_regenerate_id')) {
    session_regenerate_id(true);
}

// Store the user ID in session
Session::set(AUTH_SESSION, $user->id);

// CHECK VERIFICATION & ROLE STATUS
if (empty($user->email_verified_at)) {
    Response::redirect('verify');
    exit;
}

if (function_exists('authorized') && authorized($user->role)) {
    Response::redirect('admin/dashboard');
    exit;
}

// DEFAULT FULLY VERIFIED USER REDIRECT
Response::redirect('dashboard');
exit;