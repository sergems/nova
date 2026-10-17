<?php
/** Role-aware polling for visitor chat and staff inboxes. */
require_once __DIR__ . '/../../includes/chat-auth.php';
nova_chat_start_session();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function messagesResponse(bool $success, $data = null, string $message = '', int $status = 200): void
{
    http_response_code($status);
    $payload = ['success' => $success];
    if ($data !== null) $payload['data'] = $data;
    if ($message !== '') $payload['message'] = $message;
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) messagesResponse(false, null, 'Method not allowed.', 405);

try {
    $requestData = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== false) {
        $decoded = json_decode((string)file_get_contents('php://input'), true);
        if (is_array($decoded)) $requestData = $decoded;
    }
    $clientId = trim((string)($_GET['client_id'] ?? $_POST['client_id'] ?? $requestData['client_id'] ?? ''));
    $lastMessageTime = trim((string)($_GET['last_message_time'] ?? $_POST['last_message_time'] ?? $requestData['last_message_time'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9_-]{1,100}$/', $clientId)) messagesResponse(false, null, 'Valid client ID is required.', 400);

    // Staff authorization is derived only from the PHP session, never request JSON.
    $staff = nova_chat_staff_context();
    if ($staff === null && ($_SESSION['admin_logged_in'] ?? false) === true) {
        messagesResponse(false, null, 'Staff account is inactive or the session has expired.', 401);
    }
    $role = $staff['role'] ?? 'visitor';
    $assignments = $staff ? nova_chat_json_read('chat-assignments.json', []) : [];
    $agentAssignments = $role === 'agent'
        ? array_filter($assignments, static fn($username) => $username === $staff['username'])
        : [];

    $storage = nova_chat_storage_dir();
    $messagesFile = $storage . '/chat-messages.json';
    if (!is_file($messagesFile) && file_put_contents($messagesFile, '[]', LOCK_EX) === false) {
        messagesResponse(false, null, 'Chat storage is unavailable.', 500);
    }
    $messagesLock = fopen($storage . '/messages.lock', 'c+');
    if ($messagesLock === false || !flock($messagesLock, LOCK_SH)) {
        if (is_resource($messagesLock)) fclose($messagesLock);
        messagesResponse(false, null, 'System busy, please try again.', 503);
    }
    $decodedMessages = json_decode((string)file_get_contents($messagesFile), true);
    $messages = is_array($decodedMessages) ? $decodedMessages : [];
    $lastTime = $lastMessageTime !== '' ? (strtotime($lastMessageTime) ?: 0) : 0;
    $filteredMessages = [];

    foreach ($messages as $message) {
        if (!is_array($message)) continue;
        $messageTime = strtotime((string)($message['timestamp'] ?? '')) ?: 0;
        if ($messageTime < $lastTime) continue;

        if ($role === 'admin') {
            $filteredMessages[] = $message;
        } elseif ($role === 'agent') {
            $conversationId = !empty($message['isFromAdmin'])
                ? (string)($message['targetClientId'] ?? '')
                : (string)($message['clientId'] ?? '');
            if ($conversationId !== '' && ($assignments[$conversationId] ?? '') === $staff['username']) {
                $filteredMessages[] = $message;
            }
        } else {
            $ownMessage = (string)($message['clientId'] ?? '') === $clientId && empty($message['isFromAdmin']);
            $targetedReply = !empty($message['isFromAdmin']) && (string)($message['targetClientId'] ?? '') === $clientId;
            if ($ownMessage || $targetedReply) $filteredMessages[] = $message;
        }
    }
    usort($filteredMessages, static fn($a, $b) => (strtotime((string)($a['timestamp'] ?? '')) ?: 0) <=> (strtotime((string)($b['timestamp'] ?? '')) ?: 0));
    flock($messagesLock, LOCK_UN);
    fclose($messagesLock);

    $onlineFile = $storage . '/online-clients.json';
    $onlineLock = fopen($storage . '/online.lock', 'c+');
    if ($onlineLock === false || !flock($onlineLock, LOCK_EX)) {
        if (is_resource($onlineLock)) fclose($onlineLock);
        messagesResponse(false, null, 'Could not update online status.', 503);
    }
    $onlineClients = [];
    if (is_file($onlineFile)) {
        $decodedOnline = json_decode((string)file_get_contents($onlineFile), true);
        if (is_array($decodedOnline)) $onlineClients = $decodedOnline;
    }
    $onlineClients[$clientId] = [
        'lastSeen' => time(),
        // Legacy status field means staff, retained for existing visitor availability UI.
        'isAdmin' => $staff !== null,
        'role' => $role,
        'username' => $staff['username'] ?? null,
        'displayName' => $staff['displayName'] ?? null,
    ];
    $now = time();
    foreach ($onlineClients as $id => $client) {
        if (!is_array($client) || ($now - (int)($client['lastSeen'] ?? 0)) > 300) unset($onlineClients[$id]);
    }
    file_put_contents($onlineFile, json_encode($onlineClients), LOCK_EX);
    flock($onlineLock, LOCK_UN);
    fclose($onlineLock);

    $userCount = 0;
    $staffCount = 0;
    foreach ($onlineClients as $client) {
        if (!empty($client['isAdmin'])) $staffCount++;
        else $userCount++;
    }
    $responseData = [
        'messages' => $filteredMessages,
        'onlineStatus' => ['userCount' => $userCount, 'adminCount' => $staffCount, 'isOnline' => $staffCount > 0],
        'serverTime' => date('c'),
        'role' => $role,
    ];
    if ($role === 'admin') $responseData['assignments'] = (object)$assignments;
    if ($role === 'agent') {
        $responseData['assignments'] = (object)$agentAssignments;
        $responseData['assignedChatIds'] = array_values(array_map('strval', array_keys($agentAssignments)));
    }
    messagesResponse(true, $responseData);
} catch (Throwable $error) {
    error_log('Get messages error: ' . $error->getMessage());
    messagesResponse(false, null, 'Internal server error.', 500);
}
