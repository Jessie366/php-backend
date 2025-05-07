<?php
$host = "postgres.railway.internal";
$port = "5432";
$dbname = "railway";
$user = "postgres";
$password = "bwYJOeTBobRUOZPaOCEITywQwSlcNrqd";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
