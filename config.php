<?php
declare(strict_types=1);

function env_value(string $key, ?string $fallback = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $fallback;
    }
    return $value;
}

function send_json($payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function configure_cors(array $methods, array $extraHeaders = []): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_filter(array_map(
        'trim',
        explode(',', env_value('ALLOWED_ORIGINS', ''))
    ));

    if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
        header("Access-Control-Allow-Origin: {$origin}");
        header('Vary: Origin');
    }

    $headers = array_merge(['Content-Type', 'Authorization'], $extraHeaders);
    header('Access-Control-Allow-Methods: ' . implode(', ', $methods));
    header('Access-Control-Allow-Headers: ' . implode(', ', array_unique($headers)));

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function require_method(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        send_json(['error' => 'Method not allowed.'], 405);
    }
}

function db_connect()
{
    $databaseUrl = env_value('DATABASE_URL');
    if ($databaseUrl !== null) {
        $conn = pg_connect($databaseUrl);
    } else {
        $host = env_value('PGHOST', 'postgres.railway.internal');
        $db = env_value('PGDATABASE', 'railway');
        $user = env_value('PGUSER', 'postgres');
        $password = env_value('PGPASSWORD');
        $port = env_value('PGPORT', '5432');

        if ($password === null) {
            send_json(['error' => 'Database password is not configured.'], 500);
        }

        $conn = pg_connect(sprintf(
            'host=%s dbname=%s user=%s password=%s port=%s',
            $host,
            $db,
            $user,
            $password,
            $port
        ));
    }

    if (!$conn) {
        send_json(['error' => 'Failed to connect to the database.'], 500);
    }

    return $conn;
}

function read_json_body(): array
{
    $body = file_get_contents('php://input');
    $data = json_decode($body ?: '{}', true);
    if (!is_array($data)) {
        send_json(['error' => 'Invalid JSON body.'], 400);
    }
    return $data;
}

function require_fields(array $data, array $fields): array
{
    $values = [];
    foreach ($fields as $field) {
        $value = trim((string)($data[$field] ?? ''));
        if ($value === '') {
            send_json(['error' => "Missing field: {$field}"], 400);
        }
        $values[$field] = $value;
    }
    return $values;
}
?>
