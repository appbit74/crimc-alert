<?php
// db.php

// เรียกใช้ Autoloader และ Dotenv
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ตั้งค่าการเชื่อมต่อฐานข้อมูลจาก .env
$db_host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_DATABASE'];
$db_user = $_ENV['DB_USERNAME'];
$db_pass = $_ENV['DB_PASSWORD'];
$db_port = $_ENV['DB_PORT'];

// สร้างการเชื่อมต่อ PDO
try {
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // ในระบบจริง ควร log error แทนการ echo ออกมาตรงๆ
    http_response_code(500);
    die("Database connection failed: " . $e->getMessage());
}
?>