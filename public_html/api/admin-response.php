<?php
/** Staff replies to visitor chat conversations. */
require_once __DIR__ . '/../../includes/chat-auth.php';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function replyResponse(bool $success, $data = null, string $message = '', int $status = 200): void
{
    http_response_code($status);
    $payload = ['success' => $success];
    if ($data !== null) $payload['data'] = $data;
    if ($message !== '') $payload['message'] = $message;
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') replyResponse(false, null, 'Method not allowed.', 405);
$staff = nova_chat_require_staff();

try {
    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) replyResponse(false, null, 'Invalid JSON.', 400);
    $targetClientId = trim((string)($input['targetClientId'] ?? ''));
    $message = trim((string)($input['message'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9_-]{1,80}$/', $targetClientId)) replyResponse(false, null, 'Valid target chat is required.', 400);
    if ($message === '') replyResponse(false, null, 'Message cannot be empty.', 400);
    if (strlen($message) > 1000) replyResponse(false, null, 'Message too long (max 1000 characters).', 400);

    if ($staff['role'] === 'agent') {
        $assignments = nova_chat_json_read('chat-assignments.json', []);
        if (($assignments[$targetClientId] ?? '') !== $staff['username']) {
            replyResponse(false, null, 'This chat is not assigned to your account.', 403);
        }
    }

    $storage = nova_chat_storage_dir();
    $messagesFile = $storage . '/chat-messages.json';
    if (!is_file($messagesFile)) file_put_contents($messagesFile, '[]', LOCK_EX);
    $messagesLock = fopen($storage . '/messages.lock', 'c+');
    if ($messagesLock === false || !flock($messagesLock, LOCK_EX)) {
        if (is_resource($messagesLock)) fclose($messagesLock);
        replyResponse(false, null, 'System busy, please try again.', 503);
    }
    $decoded = json_decode((string)file_get_contents($messagesFile), true);
    $messages = is_array($decoded) ? $decoded : [];
    $timestamp = date('c');
    $messageData = [
        'type' => 'admin_response',
        'clientId' => $staff['clientId'],
        'userId' => $staff['clientId'],
        'userName' => 'Nova Support (' . $staff['displayName'] . ')',
        'message' => $message,
        'timestamp' => $timestamp,
        'isFromAdmin' => true,
        'targetClientId' => $targetClientId,
        'senderUsername' => $staff['username'],
        'id' => uniqid('staff_msg_', true),
    ];
    $messages[] = $messageData;
    if (count($messages) > 1000) $messages = array_slice($messages, -1000);
    $encoded = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($encoded === false || file_put_contents($messagesFile, $encoded, LOCK_EX) === false) {
        flock($messagesLock, LOCK_UN);
        fclose($messagesLock);
        replyResponse(false, null, 'Failed to save response.', 500);
    }
    flock($messagesLock, LOCK_UN);
    fclose($messagesLock);

    $onlineLock = fopen($storage . '/online.lock', 'c+');
    if (is_resource($onlineLock) && flock($onlineLock, LOCK_EX)) {
        $onlineFile = $storage . '/online-clients.json';
        $onlineClients = [];
        if (is_file($onlineFile)) {
            $decodedOnline = json_decode((string)file_get_contents($onlineFile), true);
            if (is_array($decodedOnline)) $onlineClients = $decodedOnline;
        }
        $onlineClients[$staff['clientId']] = [
            'lastSeen' => time(), 'isAdmin' => true, 'role' => $staff['role'],
            'username' => $staff['username'], 'displayName' => $staff['displayName'],
        ];
        file_put_contents($onlineFile, json_encode($onlineClients), LOCK_EX);
        flock($onlineLock, LOCK_UN);
    }
    if (is_resource($onlineLock)) fclose($onlineLock);

    try {
        nova_chat_json_transaction('chat-activity.json', [], static function (&$activities) use ($staff, $targetClientId, $timestamp) {
            $activities[] = [
                'clientId' => $staff['clientId'], 'userName' => $staff['displayName'],
                'action' => 'admin_response', 'targetClientId' => $targetClientId,
                'timestamp' => $timestamp, 'isFromAdmin' => true,
            ];
            if (count($activities) > 100) $activities = array_slice($activities, -100);
            return null;
        });
    } catch (Throwable $ignored) {
        // A chat reply remains successful if activity logging is unavailable.
    }

    replyResponse(true, [
        'messageId' => $messageData['id'],
        'timestamp' => $timestamp,
        'targetClientId' => $targetClientId,
        'senderUsername' => $staff['username'],
    ], 'Response sent successfully.');
} catch (Throwable $error) {
    error_log('Staff reply error: ' . $error->getMessage());
    replyResponse(false, null, 'Internal server error.', 500);
}
