<?php
// ✅ 设置响应头（包括 CORS 支持）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ 预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ 设置时区（澳洲悉尼）
date_default_timezone_set('Australia/Sydney');
file_put_contents('php://stderr', "DEBUG: timezone = " . date_default_timezone_get() . "\n");

// ✅ 数据库连接配置
$host = 'postgres.railway.internal';
$db   = 'railway';
$user = 'postgres';
$pass = 'bwYJOeTBobRUOZPaOCEITywQwSlcNrqd';
$port = '5432';

// ✅ 建立连接
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// ✅ 获取 JSON 数据
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['description'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

file_put_contents('php://stderr', "DEBUG: NAME = [$name], UNIT = [$unit], DESC = [$desc], TIME = [$submitted_at]\n");

// ✅ 插入数据
$result = pg_query_params($conn,
    "INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $unit, $desc, $submitted_at]
);

// ✅ 返回响应
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
