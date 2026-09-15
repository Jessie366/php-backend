<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

configure_cors(['GET', 'OPTIONS']);
require_method('GET');
require_admin_auth();

$conn = db_connect();
$result = pg_query($conn, 'SELECT id, name, unit, description, submitted_at FROM repair_requests ORDER BY submitted_at DESC');

if (!$result) {
    pg_close($conn);
    send_json(['error' => 'Database query failed.'], 500);
}

$data = pg_fetch_all($result) ?: [];
pg_close($conn);

send_json($data);
?>
