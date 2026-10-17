<?php
/**
 * Chat Status API Endpoint
 * For getting online status and connection info
 */
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
    $secureStorageDir = __DIR__ . '/../../secure_storage';
    $onlineFile = $secureStorageDir . '/online-clients.json';
    $activityFile = $secureStorageDir . '/chat-activity.json';
    
    // Ensure secure storage directory exists
    if (!is_dir($secureStorageDir)) {
        mkdir($secureStorageDir, 0750, true);
        file_put_contents($secureStorageDir . '/.htaccess', "Order deny,allow\nDeny from all");
    }
    
    // SECURITY FIX: Add file locking for online clients
    $onlineLockFile = $secureStorageDir . '/online.lock';
    $onlineLockHandle = fopen($onlineLockFile, 'w');
    if (!flock($onlineLockHandle, LOCK_EX)) {
        fclose($onlineLockHandle);
        sendResponse(false, null, 'System busy, please try again');
    }
    
    // Get online clients
    $onlineClients = [];
    if (file_exists($onlineFile)) {
        $content = file_get_contents($onlineFile);
        if ($content) {
            $onlineClients = json_decode($content, true) ?: [];
        }
    }
    
    // Clean up old clients (not seen for more than 5 minutes)
    $currentTime = time();
    foreach ($onlineClients as $id => $client) {
        if (($currentTime - $client['lastSeen']) > 300) {
            unset($onlineClients[$id]);
        }
    }
    
    // Save cleaned up data
    file_put_contents($onlineFile, json_encode($onlineClients));
    flock($onlineLockHandle, LOCK_UN);
    fclose($onlineLockHandle);
    
    // Count users and admins
    $userCount = 0;
    $adminCount = 0;
    $activeClients = [];
    
    foreach ($onlineClients as $clientId => $client) {
        if ($client['isAdmin']) {
            $adminCount++;
        } else {
            $userCount++;
            $activeClients[] = [
                'clientId' => $clientId,
                'lastSeen' => $client['lastSeen'],
                'isActive' => ($currentTime - $client['lastSeen']) < 60 // Active in last minute
            ];
        }
    }
    
    // SECURITY FIX: Add file locking for activity file
    $activityLockFile = $secureStorageDir . '/activity.lock';
    $activityLockHandle = fopen($activityLockFile, 'w');
    $recentActivity = [];
    
    if (flock($activityLockHandle, LOCK_SH)) {
        if (file_exists($activityFile)) {
            $content = file_get_contents($activityFile);
            if ($content) {
                $activities = json_decode($content, true) ?: [];
                // Get last 10 activities from the last hour
                $oneHourAgo = time() - 3600;
                $recentActivity = array_filter($activities, function($activity) use ($oneHourAgo) {
                    return strtotime($activity['timestamp']) > $oneHourAgo;
                });
                $recentActivity = array_slice($recentActivity, -10);
            }
        }
        flock($activityLockHandle, LOCK_UN);
    }
    fclose($activityLockHandle);
    
    // Check if support is available (at least one admin online)
    $supportAvailable = $adminCount > 0;
    
    sendResponse(true, [
        'onlineStatus' => [
            'userCount' => $userCount,
            'adminCount' => $adminCount,
            'isOnline' => $supportAvailable,
            'supportAvailable' => $supportAvailable
        ],
        'activeClients' => $activeClients,
        'recentActivity' => array_values($recentActivity),
        'serverTime' => date('c'),
        'lastUpdated' => time()
    ]);
    
} catch (Exception $e) {
    error_log('Chat status error: ' . $e->getMessage());
    sendResponse(false, null, 'Internal server error');
}
?>