<?php
/** Admin-only management of live-chat agent accounts. */
require_once __DIR__ . '/../../includes/chat-auth.php';
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function usersResponse(bool $success, $data = null, string $message = '', int $status = 200): void
{
    http_response_code($status);
    $payload = ['success' => $success];
    if ($data !== null) $payload['data'] = $data;
    if ($message !== '') $payload['message'] = $message;
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) usersResponse(false, null, 'Method not allowed', 405);
nova_chat_require_staff(true);

function publicAgentList(array $users): array
{
    $agents = [];
    foreach ($users as $user) {
        if (!is_array($user) || ($user['role'] ?? '') !== 'agent') continue;
        $agents[] = [
            'username' => (string)($user['username'] ?? ''),
            'displayName' => (string)($user['displayName'] ?? $user['username'] ?? ''),
            'active' => ($user['active'] ?? false) === true,
            'createdAt' => (string)($user['createdAt'] ?? ''),
        ];
    }
    usort($agents, static fn($a, $b) => strcasecmp($a['displayName'], $b['displayName']));
    return $agents;
}

function removeAgentAssignments(string $username): void
{
    nova_chat_json_transaction('chat-assignments.json', [], static function (&$assignments) use ($username) {
        foreach ($assignments as $chatId => $assignedUsername) {
            if ($assignedUsername === $username) unset($assignments[$chatId]);
        }
        return null;
    });
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $users = nova_chat_json_read('chat-users.json', []);
        usersResponse(true, ['agents' => publicAgentList($users)]);
    }

    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) usersResponse(false, null, 'Invalid JSON', 400);
    $action = (string)($input['action'] ?? '');

    if ($action === 'create') {
        $username = strtolower(trim((string)($input['username'] ?? '')));
        $displayName = trim((string)($input['displayName'] ?? ''));
        $password = (string)($input['password'] ?? '');
        if (!preg_match('/^[a-z0-9._-]{3,32}$/', $username)) usersResponse(false, null, 'Username must be 3–32 lowercase letters, numbers, dots, underscores or hyphens.', 400);
        if (strlen($displayName) < 2 || strlen($displayName) > 60) usersResponse(false, null, 'Display name must be 2–60 characters.', 400);
        if (strlen($password) < 12 || strlen($password) > 128) usersResponse(false, null, 'Agent passwords must be at least 12 characters.', 400);
        $result = nova_chat_json_transaction('chat-users.json', [], static function (&$users) use ($username, $displayName, $password) {
            if (isset($users[$username])) return ['error' => 'That username already exists.'];
            $users[$username] = [
                'username' => $username,
                'displayName' => $displayName,
                'role' => 'agent',
                'active' => true,
                'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
                'createdAt' => date('c'),
                'updatedAt' => date('c'),
            ];
            return ['user' => $users[$username]];
        });
        if (isset($result['error'])) usersResponse(false, null, $result['error'], 409);
        usersResponse(true, ['agents' => publicAgentList(nova_chat_json_read('chat-users.json', []))], 'Agent account created.');
    }

    if ($action === 'update') {
        $username = strtolower(trim((string)($input['username'] ?? '')));
        $displayName = trim((string)($input['displayName'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $hasActive = array_key_exists('active', $input);
        $active = $hasActive ? filter_var($input['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        if ($username === 'admin' || $username === '') usersResponse(false, null, 'Only agent accounts can be changed.', 400);
        if ($displayName !== '' && (strlen($displayName) < 2 || strlen($displayName) > 60)) usersResponse(false, null, 'Display name must be 2–60 characters.', 400);
        if ($password !== '' && (strlen($password) < 12 || strlen($password) > 128)) usersResponse(false, null, 'Agent passwords must be at least 12 characters.', 400);
        if ($hasActive && $active === null) usersResponse(false, null, 'Invalid active status.', 400);
        $result = nova_chat_json_transaction('chat-users.json', [], static function (&$users) use ($username, $displayName, $password, $hasActive, $active) {
            if (!isset($users[$username]) || !is_array($users[$username]) || ($users[$username]['role'] ?? '') !== 'agent') return ['error' => 'Agent account not found.'];
            if ($displayName !== '') $users[$username]['displayName'] = $displayName;
            if ($password !== '') $users[$username]['passwordHash'] = password_hash($password, PASSWORD_DEFAULT);
            if ($hasActive) $users[$username]['active'] = $active;
            $users[$username]['updatedAt'] = date('c');
            return ['active' => ($users[$username]['active'] ?? false) === true];
        });
        if (isset($result['error'])) usersResponse(false, null, $result['error'], 404);
        if ($hasActive && !$active) removeAgentAssignments($username);
        usersResponse(true, ['agents' => publicAgentList(nova_chat_json_read('chat-users.json', []))], 'Agent account updated.');
    }

    if ($action === 'delete') {
        $username = strtolower(trim((string)($input['username'] ?? '')));
        if ($username === '' || $username === 'admin') usersResponse(false, null, 'The administrator account cannot be removed.', 400);
        $result = nova_chat_json_transaction('chat-users.json', [], static function (&$users) use ($username) {
            if (!isset($users[$username]) || !is_array($users[$username]) || ($users[$username]['role'] ?? '') !== 'agent') return false;
            unset($users[$username]);
            return true;
        });
        if (!$result) usersResponse(false, null, 'Agent account not found.', 404);
        removeAgentAssignments($username);
        usersResponse(true, ['agents' => publicAgentList(nova_chat_json_read('chat-users.json', []))], 'Agent account deleted.');
    }

    usersResponse(false, null, 'Unknown account action.', 400);
} catch (Throwable $error) {
    error_log('Agent account management error: ' . $error->getMessage());
    usersResponse(false, null, 'Could not update agent accounts.', 500);
}
