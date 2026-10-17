<?php
/**
 * Shared helpers for Nova's staff chat APIs.
 * Runtime account, assignment, and message data stays outside public_html.
 */

function nova_chat_storage_dir(): string
{
    $directory = dirname(__DIR__) . '/secure_storage';
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Private chat storage is unavailable.');
    }
    return $directory;
}

function nova_chat_json_path(string $filename): string
{
    if (basename($filename) !== $filename || !preg_match('/^[a-z0-9._-]+\.json$/i', $filename)) {
        throw new InvalidArgumentException('Invalid private storage filename.');
    }
    return nova_chat_storage_dir() . '/' . $filename;
}

function nova_chat_json_read(string $filename, array $fallback = []): array
{
    $path = nova_chat_json_path($filename);
    $lock = fopen($path . '.lock', 'c+');
    if ($lock === false || !flock($lock, LOCK_SH)) {
        if (is_resource($lock)) fclose($lock);
        throw new RuntimeException('Could not read private chat storage.');
    }

    $data = $fallback;
    if (is_file($path)) {
        $contents = file_get_contents($path);
        $decoded = $contents === false ? null : json_decode($contents, true);
        if (is_array($decoded)) $data = $decoded;
    }

    flock($lock, LOCK_UN);
    fclose($lock);
    return $data;
}

/**
 * Execute a read/modify/write transaction under an exclusive lock.
 * The callback receives the decoded JSON array by reference.
 */
function nova_chat_json_transaction(string $filename, array $fallback, callable $callback)
{
    $path = nova_chat_json_path($filename);
    $lock = fopen($path . '.lock', 'c+');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        if (is_resource($lock)) fclose($lock);
        throw new RuntimeException('Could not update private chat storage.');
    }

    $data = $fallback;
    if (is_file($path)) {
        $contents = file_get_contents($path);
        $decoded = $contents === false ? null : json_decode($contents, true);
        if (is_array($decoded)) $data = $decoded;
    }

    try {
        $result = $callback($data);
        $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) throw new RuntimeException('Could not encode private chat storage.');

        $temporary = $path . '.tmp.' . bin2hex(random_bytes(8));
        if (file_put_contents($temporary, $encoded, LOCK_EX) === false) {
            throw new RuntimeException('Could not save private chat storage.');
        }
        @chmod($temporary, 0640);
        if (!rename($temporary, $path)) {
            @unlink($temporary);
            throw new RuntimeException('Could not replace private chat storage.');
        }
        return $result;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function nova_chat_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;

    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]));
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $forwardedProto === 'https'
        || str_ends_with($host, '.manus.computer');
    $current = session_get_cookie_params();

    session_set_cookie_params([
        'lifetime' => $current['lifetime'],
        'path' => '/',
        'domain' => $current['domain'],
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => $isHttps ? 'None' : 'Lax',
    ]);
    session_start();
}

function nova_chat_staff_context(): ?array
{
    nova_chat_start_session();
    if (($_SESSION['admin_logged_in'] ?? false) !== true) return null;

    $loginTime = (int)($_SESSION['admin_login_time'] ?? 0);
    if ($loginTime > 0 && (time() - $loginTime) > 4 * 60 * 60) {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
        return null;
    }

    $username = (string)($_SESSION['admin_username'] ?? '');
    $role = (string)($_SESSION['admin_role'] ?? ($username === 'admin' ? 'admin' : 'agent'));
    if ($username === '' || !in_array($role, ['admin', 'agent'], true)) return null;

    try {
        $users = nova_chat_json_read('chat-users.json', []);
        $account = $users[$username] ?? null;
        if (!is_array($account) || ($account['active'] ?? false) !== true || ($account['role'] ?? '') !== $role) return null;
    } catch (Throwable $error) {
        return null;
    }

    return [
        'username' => $username,
        'displayName' => (string)($account['displayName'] ?? $_SESSION['admin_display_name'] ?? $username),
        'role' => $role,
        'clientId' => (string)($_SESSION['admin_client_id'] ?? ''),
    ];
}

function nova_chat_require_staff(bool $adminOnly = false): array
{
    $staff = nova_chat_staff_context();
    if ($staff === null) {
        nova_chat_json_error(401, 'Staff authentication required.');
    }
    if ($adminOnly && $staff['role'] !== 'admin') {
        nova_chat_json_error(403, 'Administrator access required.');
    }
    return $staff;
}

function nova_chat_json_error(int $status, string $message): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}
