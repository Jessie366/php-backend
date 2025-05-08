<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

$result = pg_query($conn, "SELECT * FROM repair_requests ORDER BY id DESC");
$data = pg_fetch_all($result);
echo json_encode($data);

pg_close($conn);
?>

