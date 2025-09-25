<?php
// save-subscription.php

header('Content-Type: application/json');

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/db.php';

// รับข้อมูล JSON จาก Client
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['endpoint'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid subscription data.']);
    exit;
}

try {
    $endpoint = $data['endpoint'];
    $p256dh = $data['keys']['p256dh'];
    $auth = $data['keys']['auth'];

    // ใช้ ON DUPLICATE KEY UPDATE เพื่อป้องกันข้อมูลซ้ำซ้อนและอัปเดตข้อมูลล่าสุด
    // คำสั่งนี้จะทำงานเมื่อ endpoint ซ้ำ: จะอัปเดต p256dh และ auth ของแถวที่มีอยู่
    $stmt = $pdo->prepare(
        "INSERT INTO subscriptions (endpoint, p256dh, auth) 
         VALUES (?, ?, ?) 
         ON DUPLICATE KEY UPDATE p256dh = VALUES(p256dh), auth = VALUES(auth)"
    );

    $stmt->execute([$endpoint, $p256dh, $auth]);

    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Subscription saved successfully.']);

} catch (PDOException $e) {
    http_response_code(500);
    // ในระบบจริง ควร log error
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>