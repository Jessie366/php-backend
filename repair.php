<?php
// ✅ Set response headers (including CORS support)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ Set timezone (Australia/Sydney)
date_default_timezone_set('Australia/Sydney');
file_put_contents('php://stderr', "DEBUG: timezone = " . date_default_timezone_get() . "\n");

// ✅ Database connection config
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// ✅ Establish connection
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// ✅ Retrieve JSON data
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['description'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

file_put_contents('php://stderr', "DEBUG: NAME = [$name], UNIT = [$unit], DESC = [$desc], TIME = [$submitted_at]\n");

// ✅ Insert data into the database
$result = pg_query_params($conn,
    "INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $unit, $desc, $submitted_at]
);

// ✅ Return response
if ($result) {
    $row = pg_fetch_assoc($result);
    echo json_encode([
        "status" => "success",
        "message" => "Repair request submitted.",
        "repairRequest" => ["id" => $row['id']]
    ]);
} else {
    echo json_encode(["error" => "Database insert failed."]);
}

pg_close($conn);
?>
