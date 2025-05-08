<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 数据库配置
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// 连接数据库
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// 查询数据
$result = pg_query($conn, "SELECT * FROM contact_messages ORDER BY submitted_at DESC");
$data = pg_fetch_all($result);
echo json_encode($data);
pg_close($conn);
?>
