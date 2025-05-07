<?php
// CORS headers (重要！解决跨域问题)
header('Access-Control-Allow-Origin: *');  // 允许任何域访问
header('Access-Control-Allow-Methods: POST, OPTIONS');  // 允许的方法
header('Access-Control-Allow-Headers: Content-Type');  // 允许的 header 类型
header('Content-Type: application/json');

// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database config
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// Connect to database
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// Read POST body
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

// Insert into contact_messages
$result = pg_query_params($conn,
    "INSERT INTO contact_messages (name, email, message_new, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $email, $message, $submitted_at]
);

if ($result) {
    $row = pg_fetch_assoc($result);
    echo json_encode([
        "status" => "success",
        "message" => "Message submitted.",
        "id" => $row['id']
    ]);
} else {
    echo json_encode(["error" => "Database insert failed."]);
}

pg_close($conn);
?>
