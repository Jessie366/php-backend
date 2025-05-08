<?php
file_put_contents('php://stderr', "RECEIVED: " . json_encode($data) . "\n");
file_put_contents('php://stderr', "DEBUG: desc = $desc\n");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ Preflight check
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ Database config
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// ✅ Connect to DB
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// ✅ Read JSON data
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['description'] ?? '';  // ✅ 修正字段名为 description
$submitted_at = date('Y-m-d H:i:s');

// ✅ Insert to DB
$result = pg_query_params($conn,
    "INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $unit, $desc, $submitted_at]
);

if ($result) {
    $row = pg_fetch_assoc($result);
    echo json_encode([
        "status" => "success",
        "message" => "Repair request submitted.",
        "repairRequest" => [
            "id" => $row['id']
        ]
    ]);
} else {
    echo json_encode(["error" => "Database insert failed."]);
}

pg_close($conn);
?>
