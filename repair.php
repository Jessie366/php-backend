<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

configure_cors(['POST', 'OPTIONS']);
require_method('POST');

date_default_timezone_set('Australia/Sydney');

$data = read_json_body();
$fields = require_fields($data, ['name', 'unit', 'description']);
$submittedAt = date('Y-m-d H:i:s');

$conn = db_connect();
$result = pg_query_params(
    $conn,
    'INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id',
    [$fields['name'], $fields['unit'], $fields['description'], $submittedAt]
);

if (!$result) {
    pg_close($conn);
    send_json(['error' => 'Database insert failed.'], 500);
}

$row = pg_fetch_assoc($result);
pg_close($conn);

send_json([
    'status' => 'success',
    'message' => 'Repair request submitted.',
    'repairRequest' => ['id' => $row['id'] ?? null],
]);
?>
