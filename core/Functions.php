<?php
/**
 * Owl Framework - Global Helper Functions
 * These are available everywhere in the app.
 */

defined('ROOTPATH') OR die("Direct access denied.");
function owlversion(){
    echo ("Owl v1.0");
}

/**
 * Generates or retrieves a unique CSP Nonce for the current request.
 */
function get_nonce() {
    static $nonce = null;
    if ($nonce === null) {
        // Generate a cryptographically secure random string
        $nonce = base64_encode(bin2hex(random_bytes(16)));
    }
    return $nonce;
}

/**
 * Tier 1: The General Bouncer
 * Checks if the user is logged in at all.
 */
function user($redirect = false) {
    static $user_data = null; 
    
    if ($user_data === null && \Core\Session::has(AUTH_SESSION)) {
        if (!class_exists('Register') && !class_exists('User')) {
            throw new \Exception("Framework Error: You must create either a 'User' or 'Register' model for authentication to function.");
        }

        $modelName = class_exists('User') ? 'User' : 'Register';
        $model = new $modelName();
        $user_data = $model->first(['id' => \Core\Session::get(AUTH_SESSION)]);
    }

    if ($redirect && $user_data === null) {
        \Core\Session::flash('error', 'Please login to access this page.');
        \Core\Response::redirect('login');
        exit;
    }
    
    return $user_data;
}

function authorized($user_role){
    $allowed = ['admin', 'staff', 'super_admin'];
    return in_array($user_role, $allowed);
}

// isAdmin is user to check role before granting access to user account
function isAdmin($user_role){
    $allowed = ['admin','super_admin'];
    return in_array($user_role, $allowed);
}


function admin($redirect = false) {
    $access = user($redirect); 

    if ($access && !authorized($access->role)) {
        if ($redirect) {
            \Core\Response::redirect('../dashboard'); 
            exit;
        }
        return false;
    }

    return $access;
}

function view($data) {
    echo "<pre>";
    print($data);
    echo "</pre>";
}

/**
 * Owl Framework Core: Smart Output Helper
 * Renders raw strings, formats arrays/objects to JSON, and prints booleans.
 */
function render($data, $escape = false, $return = false) {
    $output = '';

    if (is_array($data) || is_object($data)) {
        $output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } elseif (is_bool($data)) {
        $output = $data ? 'true' : 'false';
    } else {
        $output = (string) $data;
        if ($escape) {
            $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8', false);
        }
    }

    if ($return) {
        return $output;
    }

    echo $output;
}

/**
 * THE SMART REDIRECT
 */
function send($path, $type = 'frontend') {
    $path = ltrim($path, '/');
    
    // Auto-detect type if the path explicitly starts with backend/ or frontend/
    if (str_starts_with($path, 'backend/')) {
        $type = 'backend';
        $path = substr($path, 8); // Remove 'backend/' prefix
    } elseif (str_starts_with($path, 'frontend/')) {
        $type = 'frontend';
        $path = substr($path, 9); // Remove 'frontend/' prefix
    }
    
    \Core\Response::redirect($path, $type);
}

/**
 * THE SECURITY SHIELD (XSS Protection)
 */
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * THE ASSET LINKER
 */
function url($path = "") {
    return UROOT . "/" . ltrim($path, "/");
}

function aurl($path = "") {
    return AROOT . "/" . ltrim($path, "/");
}

 
/**
 * CSRF PROTECTION ENGINE
 */
function startCsrfProtection() {
    ob_start(function ($html) {
        if (preg_match('/method=["\']?post["\']?/i', $html)) {
            $token = getCsrfToken();
            $input = '<input type="hidden" name="csrf_token" value="' . $token . '">';
            return preg_replace('/(<form\b[^>]*method=["\']?post["\']?[^>]*>)/i', '$1' . $input, $html);
        }
        return $html;
    });
}

function getCsrfToken() {
    if (!isset($_SESSION['csrf_token']) || time() > ($_SESSION['csrf_expire'] ?? 0)) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_expire'] = time() + 3600; // 1 Hour Expiry
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_expire'])) {
        return false;
    }
    if (time() > $_SESSION['csrf_expire']) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CLEAN USERNAME & STRINGS
 */
function clean($str) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $str);
}

/**
 * ADVANCED REGISTRATION & BOT CHECK
 */
function botCheck($email, $username) {
    $blocked = ['mailinator.com', '10minutemail.com', 'tempmail.com', 'getnada.com', 'guerrillamail.com'];
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    if (in_array($domain, $blocked)) {
        return "Disposable email addresses are not allowed.";
    }

    if (preg_match('/(http|https|www\.)/i', $username)) {
        return "Username cannot contain links.";
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        return "Username can only contain letters and numbers.";
    }

    return true;
}

function Image($file, $path, $oldImage = null, $maxSize = 5 * 1024 * 1024) {
    if (!isset($file) || $file['error'] !== 0) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }

    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = rtrim($path, '/') . '/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        if ($oldImage) {
            $oldPath = rtrim($path, '/') . '/' . $oldImage;
            if (file_exists($oldPath) && is_file($oldPath)) {
                unlink($oldPath);
            }
        }
        return $newFileName;
    }

    return false;
}

function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);            
    $hours   = round($seconds / 3600);           
    $days    = round($seconds / 86400);        

    if ($seconds <= 60) return "Just now";
    if ($minutes <= 60) return "•" . "$minutes" . "m ago";
    if ($hours <= 24) return "•" . "$hours" . "h ago";
    return "•" . "$days" . "d ago";
}

function create_slug($text, $divider = '-') {
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function generateReferralCode(int $length = 5): string {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $code;
}

function generate_seo_keywords($content, $max_keywords = 8) {
    $text = strtolower(strip_tags($content));
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $words = str_word_count($text, 1);
    
    $stop_words = [
        'the', 'and', 'of', 'to', 'a', 'in', 'is', 'that', 'it', 'on', 'for', 'as', 
        'with', 'was', 'if', 'this', 'but', 'by', 'from', 'or', 'an', 'your', 'are', 
        'be', 'at', 'about', 'more', 'how', 'you', 'my', 'not', 'will', 'can', 'their',
        'with', 'this', 'that', 'from', 'have', 'been', 'were', 'also', 'from'
    ];
    
    $filtered_words = array_filter($words, function($word) use ($stop_words) {
        return !in_array($word, $stop_words) && strlen($word) > 3;
    });
    
    $word_counts = array_count_values($filtered_words);
    arsort($word_counts);
    
    $top_words = array_slice(array_keys($word_counts), 0, $max_keywords);
    return implode(', ', $top_words);
}

function handleImageUpdate(array $file, string $uploadDirectory, ?string $oldImageName = null, array $options = []) {
    $maxSize = $options['max_size'] ?? 5 * 1024 * 1024;
    $allowedTypes = $options['allowed_types'] ?? ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        $maxMb = $maxSize / (1024 * 1024);
        error_log("Upload blocked: File size exceeds the maximum limit of {$maxMb}MB.");
        return false;
    }

    $tmpPath = $file['tmp_tmp_name'] ?? $file['tmp_name'];

    $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($tmpPath);

    if (!in_array($mimeType, $allowedTypes)) {
        error_log("Upload blocked: Unsupported file type verified: " . $mimeType);
        return false;
    }

    $uploadDirectory = rtrim($uploadDirectory, '/') . '/';
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetFilePath = $uploadDirectory . $fileName;

    if (move_uploaded_file($tmpPath, $targetFilePath)) {
        if (!empty($oldImageName)) {
            $oldFilePath = $uploadDirectory . $oldImageName;
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
        return $fileName;
    }

    return false;
}

function generateTxRef(string $prefix = 'REF'): string {
    $timeHex = uniqid(); 
    $randomHex = bin2hex(random_bytes(1)); 
    $yearPattern = date('Y'); 
    
    $rawReference = "{$prefix}-{$yearPattern}-{$timeHex}{$randomHex}";
    return strtoupper($rawReference);
}

function optimizeImage($sourcePath, $destinationPath, $maxWidth = 800, $quality = 82) {
    if (!file_exists($sourcePath)) return false;

    @ini_set('memory_limit', '256M');

    $imageInfo = getimagesize($sourcePath);
    if ($imageInfo === false) return false;

    list($originalWidth, $originalHeight) = $imageInfo;
    $mimeType = $imageInfo['mime'];

    if ($originalWidth > 6000 || $originalHeight > 6000) {
        return false; 
    }

    switch ($mimeType) {
        case 'image/jpeg': $sourceImage = imagecreatefromjpeg($sourcePath); break;
        case 'image/png':  $sourceImage = imagecreatefrompng($sourcePath); break;
        case 'image/gif':  $sourceImage = imagecreatefromgif($sourcePath); break;
        case 'image/webp': $sourceImage = imagecreatefromwebp($sourcePath); break;
        default: return false;
    }

    if (!$sourceImage) return false;

    if ($originalWidth > $maxWidth) {
        $ratio = $maxWidth / $originalWidth;
        $newWidth = $maxWidth;
        $newHeight = round($originalHeight * $ratio);
    } else {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }

    $destinationImage = imagecreatetruecolor($newWidth, $newHeight);

    if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);
        $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
        imagefilledrectangle($destinationImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    $success = imagewebp($destinationImage, $destinationPath, $quality);

    imagedestroy($sourceImage);
    imagedestroy($destinationImage);

    return $success ? $destinationPath : false;
}

function ImagePost($file, $path, $oldImage = null, $maxWidth = 800, $quality = 82) {
    if (!isset($file) || $file['error'] !== 0) {
        return false;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }

    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.webp';
    $destination = rtrim($path, '/') . '/' . $newFileName;

    if (optimizeImage($file['tmp_name'], $destination, $maxWidth, $quality)) {
        if ($oldImage) {
            $oldPath = rtrim($path, '/') . '/' . $oldImage;
            if (file_exists($oldPath) && is_file($oldPath)) {
                unlink($oldPath);
            }
        }
        return $newFileName;
    }

    return false;
} 

function ProcessImageUpdate($fileArray, $uploadDir, $oldImage = null, $maxWidth = 800, $quality = 82) {
    if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.webp';
    $targetFilePath = rtrim($uploadDir, '/') . '/' . $newFileName;

    $tmpName = $fileArray['tmp_tmp_name'] ?? $fileArray['tmp_name'];

    if (optimizeImage($tmpName, $targetFilePath, $maxWidth, $quality)) {
        if (!empty($oldImage)) {
            $fullOldPath = (strpos($oldImage, $uploadDir) === 0) 
                ? $oldImage 
                : rtrim($uploadDir, '/') . '/' . $oldImage;

            if (file_exists($fullOldPath) && is_file($fullOldPath)) {
                unlink($fullOldPath);
            }
        }
        return $newFileName;
    }

    return false;
} 


/**
 * Global Email Helper
 * Makes sending database-configured emails as simple as one line.
 */
function send_mail($to, $subject, $message) {
    $mailer = new Mail();
    return $mailer->sendEmail($to, $subject, $message);
}

/**
 * Global Model Shortcut Helper
 * Instantiates the base model and calls your existing clone-safe table() method.
 * Usage: model('users')->insert([...]);
 */
function model(string $table) {
    return (new \Core\Model())->table($table);
}

/**
 * Global Flash Message Renderer
 * Automatically checks for success or error flash messages and outputs the alert markup.
 */
function show_alerts() {
    $success = \Core\Session::flash('success');
    $error = \Core\Session::flash('error');

    if (!$success && !$error) {
        return;
    }

    $html = '<div class="alerts">';
    
    if ($success) {
        $html .= '<p class="alertsuccess">' . esc($success) . '</p>';
    }
    
    if ($error) {
        $html .= '<p class="alerterror">' . esc($error) . '</p>';
    }
    
    $html .= '</div>';

    echo $html;
}

/**
 * Verify that the incoming request is a valid POST submission.
 * Optionally checks for a specific required form field/button.
 * 
 * @param string|null $requiredKey Optional form field name (e.g., 'change_pin')
 * 
 * verify_post_request();
 * Ensures it's a POST request AND contains the 'change_pin' field/button
* verify_post_request('change_pin');
 */
function verify_post_request($requiredKey = null) {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Session::flash("error", "Invalid Request Method.");
        Response::back();
        exit;
    }

    // If a specific field/button is required, check if it exists in POST
    if ($requiredKey !== null && !isset($_POST[$requiredKey])) {
        Session::flash("error", "Unauthorized Action.");
        Response::back();
        exit;
    }
}

/**
 * Verify that the incoming request is a valid GET request.
 * Optionally checks for a specific required query parameter in the URL.
 * 
 * @param string|null $requiredParam Optional query parameter name (e.g., 'code', 'id')
 * 
 * verify_get_request();
 * verify_get_request('code');
 * $verificationCode = $_GET['code'];
 * 
 */
function verify_get_request($requiredParam = null) {
    // Check if it's a GET request
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        Session::flash("error", "Invalid Request Method.");
        Response::back();
        exit;
    }

    // If a specific query parameter is required, check if it exists in GET
    if ($requiredParam !== null && !isset($_GET[$requiredParam])) {
        Session::flash("error", "Invalid or missing link parameters.");
        Response::back();
        exit;
    }
}

 
/**
 * Owl Framework - Render an email view template and wrap it in the layout
 * 
 * @param string $viewName Path to the view file relative to views folder (e.g., 'emails/reset_password')
 * @param array $data Variables to extract into the view
 * @param string $title Email subject/heading title
 * @return string Complete HTML email
 */
function render_email($viewName, $data = [], $title = '') {
    // Extract variables so they can be used inside the view file
    extract($data);
    
    // Start output buffering to capture the view HTML
    ob_start();
    $viewFile = ROOTPATH . 'views/' . $viewName . '.temp.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        echo "<p>Email template not found.</p>";
    }
    $content = ob_get_clean();

    // Wrap the captured content in your master email layout
    return email_layout($title, $content);
}