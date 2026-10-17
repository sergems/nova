<?php
/** Chat assignment API: agents can read their own assignments; admins can manage all. */
require_once __DIR__ . '/../../includes/chat-auth.php';
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function assignmentsResponse(bool $success, $data = null, string $message = '', int $status = 200): void
{
    http_response_code($status);
    $payload = ['success' => $success];
    if ($data !== null) $payload['data'] = $data;
    if ($message !== '') $payload['message'] = $message;
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) assignmentsResponse(false, null, 'Method not allowed', 405);
$staff = nova_chat_require_staff();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $assignments = nova_chat_json_read('chat-assignments.json', []);
        if ($staff['role'] === 'agent') {
            $assignments = array_filter($assignments, static fn($username) => $username === $staff['username']);
            assignmentsResponse(true, ['assignments' => (object)$assignments, 'agents' => []]);
        }
        $users = nova_chat_json_read('chat-users.json', []);
        $agents = [];
        foreach ($users as $user) {
            if (is_array($user) && ($user['role'] ?? '') === 'agent' && ($user['active'] ?? false) === true) {
                $agents[] = ['username' => (string)$user['username'], 'displayName' => (string)($user['displayName'] ?? $user['username'])];
            }
        }
        usort($agents, static fn($a, $b) => strcasecmp($a['displayName'], $b['displayName']));
        assignmentsResponse(true, ['assignments' => (object)$assignments, 'agents' => $agents]);
    }

    if ($staff['role'] !== 'admin') assignmentsResponse(false, null, 'Administrator access required.', 403);
    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) assignmentsResponse(false, null, 'Invalid JSON.', 400);
    $chatId = trim((string)($input['chatId'] ?? ''));
    $username = strtolower(trim((string)($input['username'] ?? '')));
    if (!preg_match('/^[A-Za-z0-9_-]{1,80}$/', $chatId)) assignmentsResponse(false, null, 'Invalid chat ID.', 400);

    if ($username !== '') {
        $users = nova_chat_json_read('chat-users.json', []);
        $agent = $users[$username] ?? null;
        if (!is_array($agent) || ($agent['role'] ?? '') !== 'agent' || ($agent['active'] ?? false) !== true) {
            assignmentsResponse(false, null, 'Choose an active agent account.', 400);
        }
    }

    nova_chat_json_transaction('chat-assignments.json', [], static function (&$assignments) use ($chatId, $username) {
        if ($username === '') unset($assignments[$chatId]);
        else $assignments[$chatId] = $username;
        return null;
    });
    assignmentsResponse(true, ['chatId' => $chatId, 'username' => $username], $username === '' ? 'Chat unassigned.' : 'Chat assigned.');
} catch (Throwable $error) {
    error_log('Chat assignment error: ' . $error->getMessage());
    assignmentsResponse(false, null, 'Could not update chat assignment.', 500);
}
