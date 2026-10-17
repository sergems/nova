<?php
/**
 * Send Message API Endpoint
 * For sending chat messages via AJAX (replaces WebSocket send)
 */
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

// Basic rate limiting: 10 messages per minute per IP
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$secureStorageDir = __DIR__ . '/../../secure_storage';
$rateLimitFile = $secureStorageDir . '/rate_limit_send.json';
$currentTime = time();

// Ensure secure storage directory exists
if (!is_dir($secureStorageDir)) {
    mkdir($secureStorageDir, 0750, true);
    file_put_contents($secureStorageDir . '/.htaccess', "Order deny,allow\nDeny from all");
}

// SECURITY FIX: Add file locking for rate limiting
$rateLimitLockFile = $secureStorageDir . '/rate_limit.lock';
$rateLimitLockHandle = fopen($rateLimitLockFile, 'w');
if (!flock($rateLimitLockHandle, LOCK_EX)) {
    fclose($rateLimitLockHandle);
    sendResponse(false, null, 'System busy, please try again');
}

// Rate limiting check
$rateLimitData = [];
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
    return strpos($ip, $clientIP) === 0;
}, ARRAY_FILTER_USE_BOTH);

if (count($ipRequests) >= 10) {
    flock($rateLimitLockHandle, LOCK_UN);
    fclose($rateLimitLockHandle);
    http_response_code(429);
    sendResponse(false, null, 'Rate limit exceeded. Please wait before sending another message.');
}

// Add current request to rate limit data
$rateLimitData[$clientIP . '_' . $currentTime] = $currentTime;
file_put_contents($rateLimitFile, json_encode($rateLimitData));
flock($rateLimitLockHandle, LOCK_UN);
fclose($rateLimitLockHandle);

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, null, 'Invalid JSON');
    }
    
    // Validate required fields
    $requiredFields = ['clientId', 'message'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            sendResponse(false, null, ucfirst($field) . ' is required');
        }
    }
    
    // Store plain text; both chat interfaces render it via textContent.
    $clientId = trim((string)$input['clientId']);
    $message = trim((string)$input['message']);
    $userName = substr(trim((string)($input['userName'] ?? 'Visitor')), 0, 80);
    // Admin messages can only be created by the authenticated admin-response endpoint.
    $isFromAdmin = false;
    $targetClientId = '';

    if (!preg_match('/^[A-Za-z0-9_-]{1,80}$/', $clientId)) {
        sendResponse(false, null, 'Invalid client ID');
    }
    
    // Validate message length
    if (strlen($message) < 1) {
        sendResponse(false, null, 'Message cannot be empty');
    }
    
    if (strlen($message) > 1000) {
        sendResponse(false, null, 'Message too long (max 1000 characters)');
    }
    
    // Path to chat messages storage
    $messagesFile = $secureStorageDir . '/chat-messages.json';
    
    // Initialize messages array if file doesn't exist
    if (!file_exists($messagesFile)) {
        file_put_contents($messagesFile, json_encode([]));
    }
    
    // SECURITY FIX: Add file locking for message file operations
    $messagesLockFile = $secureStorageDir . '/messages.lock';
    $messagesLockHandle = fopen($messagesLockFile, 'w');
    if (!flock($messagesLockHandle, LOCK_EX)) {
        fclose($messagesLockHandle);
        sendResponse(false, null, 'System busy, please try again');
    }
    
    // Read existing messages
    $messages = [];
    $fileContent = file_get_contents($messagesFile);
    if ($fileContent) {
        $messages = json_decode($fileContent, true) ?: [];
    }
    
    // Create message data
    $messageData = [
        'type' => 'chat_message',
        'clientId' => $clientId,
        'userId' => $clientId,
        'userName' => $userName,
        'message' => $message,
        'timestamp' => date('c'),
        'isFromAdmin' => $isFromAdmin,
        'targetClientId' => $targetClientId,
        'id' => uniqid('msg_', true)
    ];
    
    // Add message to array
    $messages[] = $messageData;
    
    // Keep only last 1000 messages to prevent file from growing too large
    if (count($messages) > 1000) {
        $messages = array_slice($messages, -1000);
    }
    
    // Save messages
    if (file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT))) {
        
        // SECURITY FIX: Add file locking for activity file
        $activityFile = $secureStorageDir . '/chat-activity.json';
        $activityLockFile = $secureStorageDir . '/activity.lock';
        $activityLockHandle = fopen($activityLockFile, 'w');
        
        if (flock($activityLockHandle, LOCK_EX)) {
            $activities = [];
            
            if (file_exists($activityFile)) {
                $content = file_get_contents($activityFile);
                if ($content) {
                    $activities = json_decode($content, true) ?: [];
                }
            }
            
            // Add activity entry
            $activities[] = [
                'clientId' => $clientId,
                'userName' => $userName,
                'action' => 'message_sent',
                'timestamp' => date('c'),
                'isFromAdmin' => $isFromAdmin
            ];
            
            // Keep only last 100 activities
            if (count($activities) > 100) {
                $activities = array_slice($activities, -100);
            }
            
            file_put_contents($activityFile, json_encode($activities));
            flock($activityLockHandle, LOCK_UN);
        }
        fclose($activityLockHandle);
        
        // Release message file lock
        flock($messagesLockHandle, LOCK_UN);
        fclose($messagesLockHandle);
        
        sendResponse(true, [
            'messageId' => $messageData['id'],
            'timestamp' => $messageData['timestamp'],
            'saved' => true
        ], 'Message sent successfully');
        
    } else {
        flock($messagesLockHandle, LOCK_UN);
        fclose($messagesLockHandle);
        sendResponse(false, null, 'Failed to save message');
    }
    
} catch (Exception $e) {
    error_log('Send message error: ' . $e->getMessage());
    sendResponse(false, null, 'Internal server error');
}
?>
