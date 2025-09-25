<?php
// send-push.php

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล (ซึ่งจะโหลด .env ให้เอง)
require_once __DIR__ . '/db.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// ตั้งค่า VAPID จาก .env
$auth = [
    'VAPID' => [
        'subject' => $_ENV['VAPID_SUBJECT'],
        'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
        'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
    ],
];

$webPush = new WebPush($auth);

// ดึงข้อมูล Subscription ทั้งหมดจากฐานข้อมูล
$stmt = $pdo->query("SELECT * FROM subscriptions");
$subscriptions = $stmt->fetchAll();

if (empty($subscriptions)) {
    die("No subscriptions found in the database.");
}

// สร้าง Payload
$payload = json_encode([
    'title' => 'ทดสอบการแจ้งเตือนจาก MySQL',
    'body' => 'คดีหมายเลข อ.789/2568 มีนัดฟังคำพิพากษา',
    'icon' => 'assets/icons/crimc-alert-icon-192x192.png'
]);

// จัดคิวการแจ้งเตือน
foreach ($subscriptions as $sub) {
    $subscription = Subscription::create([
        'endpoint' => $sub['endpoint'],
        'publicKey' => $sub['p256dh'],
        'authToken' => $sub['auth'],
    ]);
    $webPush->queueNotification($subscription, $payload);
}

// ส่งการแจ้งเตือน
echo "Sending notifications...\n<br>";
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        echo "[v] Sent successfully to: {$endpoint}\n<br>";
    } else {
        echo "[x] Failed to send to: {$endpoint}. Reason: {$report->getReason()}\n<br>";
        // หากการส่งล้มเหลวเพราะ "410 Gone" หมายความว่า Subscription หมดอายุ
        if ($report->isSubscriptionExpired()) {
            // เขียนโค้ดลบ Subscription ที่หมดอายุออกจากฐานข้อมูล
            $pdo->prepare("DELETE FROM subscriptions WHERE endpoint = ?")->execute([$endpoint]);
            echo "-> Subscription deleted.\n<br>";
        }
    }
}
echo "Done.";
?>