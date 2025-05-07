<?php
// Enable full error reporting (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set response header to JSON
header('Content-Type: application/json');

// Database configuration
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';  // <-- Replace with your actual password if changed
$port = '5432';

// Connect to the PostgreSQL database
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

// Check connection
if (!$conn) {
    echo json_encode(["error" => "❌ Failed to connect to the database."]);
    exit;
}

// Read and decode the POST body
$data = json_decode(file_get_contents('php://input'), true);

// Validate received data
if (!$data) {
    echo json_encode(["error" => "❌ No JSON received or invalid format."]);
    exit;
}

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

// Insert data into the contact_messages table
$result = pg_query_params($conn,
    "INSERT INTO contact_messages (name, email, message_new, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $email, $message, $submitted_at]
);

// Handle insert result
if ($result) {
    $row = pg_fetch_assoc($result);
    echo json_encode([
        "status" => "✅ success",
        "message" => "Message submitted.",
        "id" => $row['id']
    ]);
} else {
    echo json_encode(["error" => "❌ Database insert failed."]);
}

// Close the database connection
pg_close($conn);
?>
