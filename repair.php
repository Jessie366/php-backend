<?php
header('Content-Type: application/json');

// Database configuration
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// Establish connection
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// Read request body
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['repairDescription'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

// Insert into database using correct field name: description
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

// Close connection
pg_close($conn);
?>
