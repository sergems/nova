<?php
/** Role-aware authentication for Nova chat staff. */
require_once __DIR__ . '/../../includes/chat-auth.php';
nova_chat_start_session();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

function sendResponse($success, $data = null, $message = '')
{
    $response = ['success' => $success];
    if ($data !== null) $response['data'] = $data;
    if ($message !== '') $response['message'] = $message;
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendResponse(false, null, 'Method not allowed');
}

$secureStorageDir = nova_chat_storage_dir();
$loginLockHandle = fopen($secureStorageDir . '/login.lock', 'c+');
if ($loginLockHandle === false || !flock($loginLockHandle, LOCK_EX)) {
    if (is_resource($loginLockHandle)) fclose($loginLockHandle);
    http_response_code(503);
    sendResponse(false, null, 'System busy, please try again');
}

try {
    $currentTime = time();
    $clientIP = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $loginAttemptsFile = $secureStorageDir . '/login_attempts.json';
    $loginAttempts = [];
    if (is_file($loginAttemptsFile)) {
        $decoded = json_decode((string)file_get_contents($loginAttemptsFile), true);
        if (is_array($decoded)) $loginAttempts = $decoded;
    }
    $loginAttempts = array_filter($loginAttempts, static function ($timestamp) use ($currentTime) {
        return is_numeric($timestamp) && ($currentTime - (int)$timestamp) < 900;
    });
    $ipAttempts = array_filter($loginAttempts, static function ($timestamp, $key) use ($clientIP) {
        return str_starts_with((string)$key, $clientIP . '_');
    }, ARRAY_FILTER_USE_BOTH);
    if (count($ipAttempts) >= 5) {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        http_response_code(429);
        sendResponse(false, null, 'Too many login attempts. Please try again in 15 minutes.');
    }

    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        http_response_code(400);
        sendResponse(false, null, 'Invalid JSON');
    }
    $username = trim((string)($input['username'] ?? ''));
    $password = (string)($input['password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9._-]{3,32}$/', $username) || $password === '') {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        http_response_code(400);
        sendResponse(false, null, 'Valid username and password are required');
    }

    // One-time migration seed. Subsequent account changes live in private JSON storage.
    $seedAccounts = [
        'admin' => [
            'username' => 'admin', 'displayName' => 'Administrator', 'role' => 'admin', 'active' => true,
            'passwordHash' => '$2y$10$t3QKoHP3nEcOMQ9i4uJO8O9VGIoSkrLzdKzQdGvJ4ct3DY2KT32GW', 'createdAt' => date('c'),
        ],
        'support' => [
            'username' => 'support', 'displayName' => 'Support Agent', 'role' => 'agent', 'active' => true,
            'passwordHash' => '$2y$10$wgHJPBV8EZzgPT7XYF4UhOQKQHWr7Nv8QhDKZMKXZJb4LlmYKxF2W', 'createdAt' => date('c'),
        ],
        'nova' => [
            'username' => 'nova', 'displayName' => 'Nova Agent', 'role' => 'agent', 'active' => true,
            'passwordHash' => '$2y$10$4e7IzDYXhpQ6vWFKjQzEb.BSLRxELGh5c9OdIU.xKsGPnFOa8e7.a', 'createdAt' => date('c'),
        ],
    ];
    if (!is_file($secureStorageDir . '/chat-users.json')) {
        nova_chat_json_transaction('chat-users.json', [], static function (&$users) use ($seedAccounts) {
            if (!$users) $users = $seedAccounts;
            return null;
        });
    }
    $users = nova_chat_json_read('chat-users.json', $seedAccounts);
    $account = $users[$username] ?? null;
    $valid = is_array($account)
        && ($account['active'] ?? false) === true
        && is_string($account['passwordHash'] ?? null)
        && password_verify($password, $account['passwordHash']);

    if (!$valid) {
        $loginAttempts[$clientIP . '_' . $currentTime] = $currentTime;
        file_put_contents($loginAttemptsFile, json_encode($loginAttempts), LOCK_EX);
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
        http_response_code(401);
        sendResponse(false, null, 'Invalid username or password');
    }

    session_regenerate_id(true);
    $role = ($account['role'] ?? '') === 'admin' ? 'admin' : 'agent';
    $displayName = trim((string)($account['displayName'] ?? $username));
    $staffClientId = 'staff_' . $username . '_' . bin2hex(random_bytes(8));
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_role'] = $role;
    $_SESSION['admin_display_name'] = $displayName;
    $_SESSION['admin_login_time'] = $currentTime;
    $_SESSION['admin_ip'] = $clientIP;
    $_SESSION['admin_client_id'] = $staffClientId;

    $onlineLock = fopen($secureStorageDir . '/online.lock', 'c+');
    if (is_resource($onlineLock) && flock($onlineLock, LOCK_EX)) {
        $onlineFile = $secureStorageDir . '/online-clients.json';
        $onlineClients = [];
        if (is_file($onlineFile)) {
            $decoded = json_decode((string)file_get_contents($onlineFile), true);
            if (is_array($decoded)) $onlineClients = $decoded;
        }
        $onlineClients[$staffClientId] = [
            'lastSeen' => $currentTime,
            'isAdmin' => true,
            'role' => $role,
            'username' => $username,
            'displayName' => $displayName,
        ];
        file_put_contents($onlineFile, json_encode($onlineClients), LOCK_EX);
        flock($onlineLock, LOCK_UN);
    }
    if (is_resource($onlineLock)) fclose($onlineLock);

    $loginAttempts = array_filter($loginAttempts, static function ($timestamp, $key) use ($clientIP) {
        return !str_starts_with((string)$key, $clientIP . '_');
    }, ARRAY_FILTER_USE_BOTH);
    file_put_contents($loginAttemptsFile, json_encode($loginAttempts), LOCK_EX);
    flock($loginLockHandle, LOCK_UN);
    fclose($loginLockHandle);

    sendResponse(true, [
        'adminClientId' => $staffClientId,
        'username' => $username,
        'displayName' => $displayName,
        'role' => $role,
        'loginTime' => $currentTime,
        'sessionValid' => true,
    ], 'Login successful');
} catch (Throwable $error) {
    if (is_resource($loginLockHandle)) {
        flock($loginLockHandle, LOCK_UN);
        fclose($loginLockHandle);
    }
    error_log('Staff authentication error: ' . $error->getMessage());
    http_response_code(500);
    sendResponse(false, null, 'Internal server error');
}
