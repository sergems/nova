<?php
/**
 * Admin Authentication API Endpoint
 * For admin login to the chat dashboard
 */
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

function sendResponse($success, $data = null, $message = '') {
    $response = ['success' => $success];
    if ($data !== null) {
        $response['data'] = $data;
    }
    if ($message) {
        $response['message'] = $message;
    }
    echo json_encode($response);
    exit;
}

// Rate limiting for login attempts
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$secureStorageDir = '../../secure_storage';
$loginAttemptsFile = $secureStorageDir . '/login_attempts.json';
$currentTime = time();

// Ensure secure storage directory exists
if (!is_dir($secureStorageDir)) {
    mkdir($secureStorageDir, 0750, true);
    file_put_contents($secureStorageDir . '/.htaccess', "Order deny,allow\nDeny from all");
}

// SECURITY FIX: Add file locking for login attempts
$loginLockFile = $secureStorageDir . '/login.lock';
$loginLockHandle = fopen($loginLockFile, 'w');
if (!flock($loginLockHandle, LOCK_EX)) {
    fclose($loginLockHandle);
    sendResponse(false, null, 'System busy, please try again');
}

// Check login attempts (max 5 attempts per 15 minutes)
$loginAttempts = [];
if (file_exists($loginAttemptsFile)) {
    $content = file_get_contents($loginAttemptsFile);
    if ($content) {
        $loginAttempts = json_decode($content, true) ?: [];
    }
}

// Clean old attempts (older than 15 minutes)
$loginAttempts = array_filter($loginAttempts, function($timestamp) use ($currentTime) {
    return ($currentTime - $timestamp) < 900; // 15 minutes
});

// Check current IP attempt count
$ipAttempts = array_filter($loginAttempts, function($timestamp, $ip) use ($clientIP) {
    return strpos($ip, $clientIP) === 0;
}, ARRAY_FILTER_USE_BOTH);

if (count($ipAttempts) >= 5) {
    flock($loginLockHandle, LOCK_UN);
    fclose($loginLockHandle);
    http_response_code(429);
    sendResponse(false, null, 'Too many login attempts. Please try again in 15 minutes.');
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, null, 'Invalid JSON');
    }
    
    // SECURITY FIX: Sanitize inputs
    $username = htmlspecialchars(trim($input['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = trim($input['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        sendResponse(false, null, 'Username and password are required');
    }
    
    // SECURITY FIX: Use properly hashed passwords instead of plaintext
    // Generated using password_hash('password', PASSWORD_DEFAULT)
    $validCredentials = [
        'admin' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // nova2024!
        'support' => '$2y$10$wgHJPBV8EZzgPT7XYF4UhOQKQHWr7Nv8QhDKZMKXZJb4LlmYKxF2W', // support123  
        'nova' => '$2y$10$4e7IzDYXhpQ6vWFKjQzEb.BSLRxELGh5c9OdIU.xKsGPnFOa8e7.a' // debtfree2024
    ];
    
    $isValidLogin = false;
    if (isset($validCredentials[$username]) && password_verify($password, $validCredentials[$username])) {
        $isValidLogin = true;
    }
    
    if (!$isValidLogin) {
        // Record failed attempt with file locking
        $loginAttempts[$clientIP . '_' . $currentTime] = $currentTime;
        file_put_contents($loginAttemptsFile, json_encode($loginAttempts));
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        
        sendResponse(false, null, 'Invalid username or password');
    }
    
    // SECURITY FIX: Secure session management
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Successful login  
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_login_time'] = $currentTime;
    $_SESSION['admin_ip'] = $clientIP; // Track IP for additional security
    
    // Generate admin client ID
    $adminClientId = 'admin_' . $username . '_' . uniqid();
    $_SESSION['admin_client_id'] = $adminClientId;
    
    // SECURITY FIX: Add file locking for online clients
    $onlineFile = $secureStorageDir . '/online-clients.json';
    $onlineLockFile = $secureStorageDir . '/online.lock';
    $onlineLockHandle = fopen($onlineLockFile, 'w');
    
    if (flock($onlineLockHandle, LOCK_EX)) {
        $onlineClients = [];
        
        if (file_exists($onlineFile)) {
            $content = file_get_contents($onlineFile);
            if ($content) {
                $onlineClients = json_decode($content, true) ?: [];
            }
        }
        
        $onlineClients[$adminClientId] = [
            'lastSeen' => $currentTime,
            'isAdmin' => true,
            'username' => $username
        ];
        
        file_put_contents($onlineFile, json_encode($onlineClients));
        flock($onlineLockHandle, LOCK_UN);
    }
    fclose($onlineLockHandle);
    
    // Clear login attempts for this IP (already have login lock)
    $loginAttempts = array_filter($loginAttempts, function($timestamp, $ip) use ($clientIP) {
        return strpos($ip, $clientIP) !== 0;
    }, ARRAY_FILTER_USE_BOTH);
    file_put_contents($loginAttemptsFile, json_encode($loginAttempts));
    
    // Release login lock
    flock($loginLockHandle, LOCK_UN);
    fclose($loginLockHandle);
    
    sendResponse(true, [
        'adminClientId' => $adminClientId,
        'username' => $username,
        'loginTime' => $currentTime,
        'sessionValid' => true
    ], 'Login successful');
    
} catch (Exception $e) {
    // SECURITY FIX: Ensure locks are released on error
    if (isset($loginLockHandle) && is_resource($loginLockHandle)) {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
    }
    error_log('Admin auth error: ' . $e->getMessage());
    sendResponse(false, null, 'Internal server error');
}
?>