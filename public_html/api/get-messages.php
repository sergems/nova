<?php
/**
 * Get Messages API Endpoint
 * For AJAX polling to retrieve new chat messages
 */
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Allow both GET and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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

try {
    // Get parameters
    $clientId = $_GET['client_id'] ?? $_POST['client_id'] ?? '';
    $lastMessageTime = $_GET['last_message_time'] ?? $_POST['last_message_time'] ?? '';
    
    // SECURITY FIX: Check admin status from PHP session, not client-supplied parameter
    $isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    
    // Validate session timeout for admins (4 hours)
    if ($isAdmin) {
        $sessionTimeout = 4 * 60 * 60; // 4 hours
        if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $sessionTimeout) {
            session_destroy();
            $isAdmin = false;
        }
    }
    
    if (empty($clientId)) {
        sendResponse(false, null, 'Client ID is required');
    }
    
    // Path to chat messages storage
    $secureStorageDir = '../../secure_storage';
    $messagesFile = $secureStorageDir . '/chat-messages.json';
    
    // Ensure secure storage directory exists
    if (!is_dir($secureStorageDir)) {
        mkdir($secureStorageDir, 0750, true);
        file_put_contents($secureStorageDir . '/.htaccess', "Order deny,allow\nDeny from all");
    }
    
    // Initialize messages array if file doesn't exist
    if (!file_exists($messagesFile)) {
        file_put_contents($messagesFile, json_encode([]));
    }
    
    // SECURITY FIX: Add file locking for concurrency protection
    $lockFile = $secureStorageDir . '/messages.lock';
    $lockHandle = fopen($lockFile, 'w');
    if (!flock($lockHandle, LOCK_SH)) {
        fclose($lockHandle);
        sendResponse(false, null, 'System busy, please try again');
    }
    
    // Read existing messages
    $messages = [];
    $fileContent = file_get_contents($messagesFile);
    if ($fileContent) {
        $messages = json_decode($fileContent, true) ?: [];
    }
    
    // SECURITY FIX: Proper message filtering with admin message protection
    $filteredMessages = [];
    $lastTime = $lastMessageTime ? strtotime($lastMessageTime) : 0;
    
    foreach ($messages as $message) {
        $messageTime = isset($message['timestamp']) ? strtotime($message['timestamp']) : 0;
        
        if ($messageTime <= $lastTime) {
            continue; // Skip old messages
        }
        
        if ($isAdmin) {
            // Admins see all messages newer than lastMessageTime
            $filteredMessages[] = $message;
        } else {
            // SECURITY FIX: Regular users only see:
            // 1. Their own messages
            // 2. Admin messages specifically targeted to them
            $isUserMessage = ($message['clientId'] === $clientId);
            $isTargetedAdminMessage = ($message['isFromAdmin'] ?? false) && 
                                     ($message['targetClientId'] ?? '') === $clientId;
            
            if ($isUserMessage || $isTargetedAdminMessage) {
                // SECURITY FIX: Sanitize message content to prevent XSS
                $sanitizedMessage = $message;
                $sanitizedMessage['message'] = htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8');
                $sanitizedMessage['userName'] = htmlspecialchars($message['userName'] ?? '', ENT_QUOTES, 'UTF-8');
                $filteredMessages[] = $sanitizedMessage;
            }
        }
    }
    
    // Sort by timestamp
    usort($filteredMessages, function($a, $b) {
        $timeA = strtotime($a['timestamp'] ?? 0);
        $timeB = strtotime($b['timestamp'] ?? 0);
        return $timeA - $timeB;
    });
    
    // Update online status for this client
    $onlineFile = $secureStorageDir . '/online-clients.json';
    $onlineClients = [];
    
    if (file_exists($onlineFile)) {
        $content = file_get_contents($onlineFile);
        if ($content) {
            $onlineClients = json_decode($content, true) ?: [];
        }
    }
    
    // Update client's last seen time
    $onlineClients[$clientId] = [
        'lastSeen' => time(),
        'isAdmin' => $isAdmin
    ];
    
    // Clean up old clients (not seen for more than 5 minutes)
    $currentTime = time();
    foreach ($onlineClients as $id => $client) {
        if (($currentTime - $client['lastSeen']) > 300) {
            unset($onlineClients[$id]);
        }
    }
    
    // SECURITY FIX: Add file locking for online clients file
    $onlineLockFile = $secureStorageDir . '/online.lock';
    $onlineLockHandle = fopen($onlineLockFile, 'w');
    if (flock($onlineLockHandle, LOCK_EX)) {
        file_put_contents($onlineFile, json_encode($onlineClients));
        flock($onlineLockHandle, LOCK_UN);
    }
    fclose($onlineLockHandle);
    
    // Release message file lock
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    
    // Get online counts
    $userCount = 0;
    $adminCount = 0;
    foreach ($onlineClients as $client) {
        if ($client['isAdmin']) {
            $adminCount++;
        } else {
            $userCount++;
        }
    }
    
    sendResponse(true, [
        'messages' => $filteredMessages,
        'onlineStatus' => [
            'userCount' => $userCount,
            'adminCount' => $adminCount,
            'isOnline' => $adminCount > 0
        ],
        'serverTime' => date('c')
    ]);
    
} catch (Exception $e) {
    error_log('Get messages error: ' . $e->getMessage());
    sendResponse(false, null, 'Internal server error');
}
?>