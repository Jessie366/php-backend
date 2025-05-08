<?php
// ✅ CORS headers：允许跨域请求
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ✅ CORS 预检请求（OPTIONS）
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

// ✅ 建立连接
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// ✅ 读取表单数据
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('php://stderr', "RECEIVED: " . json_encode($data) . "\n");

$name = $data['name'] ?? '';
$unit = $data['unit'] ?? '';
$desc = $data['repairDescription'] ?? '';
$submitted_at = date('Y-m-d H:i:s');

// ✅ 插入数据库（注意字段名是 description，不是 repair_description）
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

// ✅ 关闭连接
pg_close($conn);
?>
