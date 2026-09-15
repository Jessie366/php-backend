<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }

    if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches) !== 1) {
        return null;
    }

    return trim($matches[1]);
}

function require_admin_auth(): void
{
    $expected = env_value('ADMIN_API_TOKEN');
    $provided = bearer_token();

    if ($expected === null || $provided === null || !hash_equals($expected, $provided)) {
        send_json(['error' => 'Unauthorized.'], 401);
    }
}
?>
