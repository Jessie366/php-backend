<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ 预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ 数据库配置
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// ✅ 连接数据库
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// ✅ 正确顺序读取数据
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['description'] ?? '';
file_put_contents('php://stderr', "DESC FROM FRONTEND: $desc\n");
$submitted_at = date('Y-m-d H:i:s');

// ✅ 可选：调试用日志（现在读取完再打印）
file_put_contents('php://stderr', "RECEIVED: " . json_encode($data) . "\n");
file_put_contents('php://stderr', "DEBUG: description = $desc\n");

// ✅ 插入数据
$result = pg_query_params($conn,
    "INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $unit, $desc, $submitted_at]
);

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
