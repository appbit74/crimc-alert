<?php
session_start();
// require_once 'configs/Database.php'; // Consider if these are needed here
// require_once 'configs/func.inc.php';

if (!empty($_POST['thaid'])) {
    
    // 1. สร้าง nonce และ state
    $nonce = bin2hex(random_bytes(10));

    // 3. สร้าง URL สำหรับ Redirect
    $clientID = "YWtmSXZ4Q1ZaMFcxOGpjN3ZuZGVTWm81YmdTcGtsc2s=";
    $callbackURL = "https://modules.coj.go.th/thaid/callback.php";

    $url = "https://imauth.bora.dopa.go.th/api/v2/oauth2/auth/?" . http_build_query([
        'response_type' => 'code',
        'client_id' => $clientID,
        'redirect_uri' => $callbackURL,
        'scope' => 'pid,th_fname,th_lname,en_fname,en_lname,dob,gender,picture',
        'state' => '88ecaedcd98f5b2c93a08f19ba0b160e-'.$nonce // ใช้ state ที่สร้างขึ้นใหม่
    ]);

    // 4. Redirect ผู้ใช้ไปยัง ThaiID Authentication Page
    header("Location: " . $url);
    exit();

}

// หากไม่มี POST['thaid'] ก็อาจจะ redirect กลับไปหน้า login หรือแสดงข้อผิดพลาด
header("Location: index.html");
exit();

?>