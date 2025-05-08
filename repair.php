<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ CORS 预检请求处理
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ 数据库连接配置
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

// ✅ 读取前端发来的 JSON 数据
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['description'] ?? '';
date_default_timezone_set('Australia/Sydney');
$submitted_at = date('Y-m-d H:i:s');

// ✅ 调试输出日志（查看是否读取成功）
file_put_contents('php://stderr', "DEBUG: JSON = " . json_encode($data) . "\n");
file_put_contents('php://stderr', "DEBUG: NAME = [$name], UNIT = [$unit], DESC = [$desc]\n");

// ✅ 插入数据
$result = pg_query_params($conn,
    "INSERT INTO repair_requests (name, unit, description, submitted_at) VALUES ($1, $2, $3, $4) RETURNING id",
    [$name, $unit, $desc, $submitted_at]
);

// ✅ 返回结果
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

// ✅ 关闭连接
pg_close($conn);
?>
