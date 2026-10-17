<?php
/**
 * Admin Response API Endpoint
 * For admin responses to customer chat messages
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

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    sendResponse(false, null, 'Admin authentication required');
}

// Check session timeout (4 hours)
$sessionTimeout = 4 * 60 * 60; // 4 hours
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $sessionTimeout) {
    session_destroy();
    http_response_code(401);
    sendResponse(false, null, 'Session expired. Please login again.');
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, null, 'Invalid JSON');
    }
    
    // SECURITY FIX: Validate and sanitize required fields to prevent XSS
    $targetClientId = trim($input['targetClientId'] ?? '');
    $message = htmlspecialchars(trim($input['message'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    if (empty($targetClientId)) {
        sendResponse(false, null, 'Target client ID is required');
    }
    
    if (empty($message)) {
        sendResponse(false, null, 'Message cannot be empty');
    }
    
    if (strlen($message) > 1000) {
        sendResponse(false, null, 'Message too long (max 1000 characters)');
    }
    
    $secureStorageDir = '../../secure_storage';
    $messagesFile = $secureStorageDir . '/chat-messages.json';
    
    // Initialize messages array if file doesn't exist
    if (!file_exists($messagesFile)) {
        file_put_contents($messagesFile, json_encode([]));
    }
    
    // SECURITY FIX: Add file locking for message operations
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
    
    // Create admin response message
    $adminClientId = $_SESSION['admin_client_id'];
    $adminUsername = htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8');
    
    $messageData = [
        'type' => 'admin_response',
        'clientId' => $adminClientId,
        'userId' => $adminClientId,
        'userName' => 'Nova Support (' . $adminUsername . ')',
        'message' => $message,
        'timestamp' => date('c'),
        'isFromAdmin' => true,
        'targetClientId' => $targetClientId,
        'id' => uniqid('admin_msg_', true)
    ];
    
    // Add message to array
    $messages[] = $messageData;
    
    // Keep only last 1000 messages
    if (count($messages) > 1000) {
        $messages = array_slice($messages, -1000);
    }
    
    // Save messages
    if (file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT))) {
        
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
                'lastSeen' => time(),
                'isAdmin' => true,
                'username' => $adminUsername
            ];
            
            file_put_contents($onlineFile, json_encode($onlineClients));
            flock($onlineLockHandle, LOCK_UN);
        }
        fclose($onlineLockHandle);
        
        // SECURITY FIX: Add file locking for activity log
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
            
            $activities[] = [
                'clientId' => $adminClientId,
                'userName' => $adminUsername,
                'action' => 'admin_response',
                'targetClientId' => $targetClientId,
                'timestamp' => date('c'),
                'isFromAdmin' => true
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
            'targetClientId' => $targetClientId
        ], 'Response sent successfully');
        
    } else {
        flock($messagesLockHandle, LOCK_UN);
        fclose($messagesLockHandle);
        sendResponse(false, null, 'Failed to save response');
    }
    
} catch (Exception $e) {
    error_log('Admin response error: ' . $e->getMessage());
    sendResponse(false, null, 'Internal server error');
}
?>