<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

try {
    $host = env_value('PGHOST', 'postgres.railway.internal');
    $port = env_value('PGPORT', '5432');
    $dbname = env_value('PGDATABASE', 'railway');
    $user = env_value('PGUSER', 'postgres');
    $password = env_value('PGPASSWORD');

    if ($password === null) {
        throw new RuntimeException('PGPASSWORD is not configured.');
    }

    $pdo = new PDO("pgsql:host={$host};port={$port};dbname={$dbname}", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $e) {
    http_response_code(500);
    die('Database connection failed.');
}
?>
