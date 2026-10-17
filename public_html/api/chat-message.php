<?php
// Chat Message API Endpoint
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

// Basic rate limiting: 10 messages per minute per IP
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$secureStorageDir = __DIR__ . '/../../secure_storage';
$rateLimitFile = $secureStorageDir . '/rate_limit.json';
$currentTime = time();
$rateLimitData = [];

// Ensure secure storage directory exists for rate limiting
if (!is_dir($secureStorageDir)) {
    mkdir($secureStorageDir, 0750, true);
    // Create .htaccess to deny web access
    file_put_contents($secureStorageDir . '/.htaccess', "Order deny,allow\nDeny from all");
}

// Load existing rate limit data
if (file_exists($rateLimitFile)) {
    $existingData = file_get_contents($rateLimitFile);
    if ($existingData) {
        $rateLimitData = json_decode($existingData, true) ?: [];
    }
}

// Clean old entries (older than 1 minute)
$rateLimitData = array_filter($rateLimitData, function($timestamp) use ($currentTime) {
    return ($currentTime - $timestamp) < 60;
});

// Check current IP request count
$ipRequests = array_filter($rateLimitData, function($timestamp, $ip) use ($clientIP) {
    return $ip === $clientIP;
}, ARRAY_FILTER_USE_BOTH);

if (count($ipRequests) >= 10) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded. Please wait before sending another message.']);
    exit;
}

// Add current request to rate limit data
$rateLimitData[$clientIP . '_' . $currentTime] = $currentTime;

// Save rate limit data
file_put_contents($rateLimitFile, json_encode($rateLimitData));

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    
    // Validate required fields
    $requiredFields = ['message', 'userId', 'timestamp'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            exit;
        }
    }
    
    // Sanitize input
    $message = trim($input['message']);
    $userId = trim($input['userId']);
    $timestamp = trim($input['timestamp']);
    
    // Validate message length
    if (strlen($message) > 1000) {
        http_response_code(400);
        echo json_encode(['error' => 'Message too long. Maximum 1000 characters.']);
        exit;
    }
    
    // Basic CSRF protection: Validate timestamp is recent (within 5 minutes)
    $submittedTime = strtotime($timestamp);
    $currentTime = time();
    if (!$submittedTime || abs($currentTime - $submittedTime) > 300) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired timestamp. Please refresh and try again.']);
        exit;
    }
    
    // Create chat data structure
    $chatData = [
        'message' => $message,
        'userId' => $userId,
        'timestamp' => $timestamp,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'received_at' => date('Y-m-d H:i:s')
    ];
    
    // Use the secure storage directory (already created earlier)
    $storageDir = $secureStorageDir;
    
    // Save chat message to file with file locking
    $chatFile = $storageDir . '/chat-messages.json';
    
    // Open file for read/write with locking
    $fp = fopen($chatFile, 'c+');
    if (!$fp) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to open chat file']);
        exit;
    }
    
    // Lock file for exclusive access
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to lock chat file']);
        exit;
    }
    
    // Read existing messages while locked
    fseek($fp, 0);
    $existingData = stream_get_contents($fp);
    $chatMessages = $existingData ? (json_decode($existingData, true) ?: []) : [];
    
    // Add new message
    $chatMessages[] = $chatData;
    
    // Keep only last 1000 messages
    if (count($chatMessages) > 1000) {
        $chatMessages = array_slice($chatMessages, -1000);
    }
    
    // Write updated data
    ftruncate($fp, 0);
    fseek($fp, 0);
    $saved = fwrite($fp, json_encode($chatMessages, JSON_PRETTY_PRINT));
    
    // Release lock and close file
    flock($fp, LOCK_UN);
    fclose($fp);
    
    if ($saved === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save message']);
        exit;
    }
    
    // Log the chat message (for monitoring)
    $logMessage = "[CHAT] User: $userId | Message: $message | Time: $timestamp";
    error_log($logMessage);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Chat message received successfully',
        'data' => [
            'userId' => $userId,
            'timestamp' => $timestamp,
            'received_at' => $chatData['received_at']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>