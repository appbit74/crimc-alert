<?php
session_start();
require_once 'configs/Database.php';
require_once 'configs/func.inc.php';


if (!empty($_POST['thaid'])) {
    // ใช้ date() เพื่อดึงข้อมูลวันเวลาปัจจุบัน
    $currentDate = date('YmdHis'); // ปีเดือนวันชั่วโมงนาทีวินาที
    $microtime = microtime(true); // เวลาปัจจุบันในรูปแบบ microseconds (สามารถสร้างเลขสุ่มที่มีความละเอียดสูง)

    $randomNumber = substr(md5($currentDate . $microtime), 0, 8); // ใช้ MD5 เพื่อสร้างเลขสุ่มจากวันที่และเวลา
    // เลือกแค่ 8 ตัวแรกจาก MD5 hash
    // กำหนด URL ที่จะทำการ redirect
    //35cc6bb08e43f7f9901c983326568c0d = local_aryasearch
    //a293cdf231b90eedca31d0f687a79fb0 = aryasearch.coj.go.th
    //a3c37f133823b6521e1b8d6d90681096 = aryasearch.coj.go.th/check_notice.php
    $url = "https://imauth.bora.dopa.go.th/api/v2/oauth2/auth/?response_type=code&client_id=YWtmSXZ4Q1ZaMFcxOGpjN3ZuZGVTWm81YmdTcGtsc20&redirect_uri=https://aryasearch.coj.go.th/thaid/callback.php&scope=pid birthdate title given_name family_name title_en given_name_en family_name_en&state=a293cdf231b90eedca31d0f687a79fb0-" . $randomNumber;

    // ทำการ redirect
    header("Location: $url");
    // echo $_POST['thaid'];
    exit();
}
// ==========================
// ThaiID Authentication Template
// ==========================

// กรณีนี้สมมติว่าจะรับค่า ThaiID (เช่น จาก API ภายนอก หรือ Redirect กลับมา)
// คุณสามารถเขียนคำสั่งเชื่อมต่อ API ThaID หรือรับค่าจาก POST / GET ได้ตรงนี้
// ตัวอย่างเช่น

//$jsonData = file_get_contents('php://input');
// แปลง JSON เป็น PHP Array
//$data = json_decode($jsonData);
// Mock ข้อมูล ThaiID จาก API
/*$thaiID = $data->pid;
$birth_date = $data->birthdate;
$first_name = $data->titleTh.$data->given_name;
$last_name = $data->family_name;
$occupation = '-';
$phone = '00';
$user_type = 0; // 0 = user
*/

// รับข้อมูลจาก POST ที่ส่งมาจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่ามีการส่งข้อมูลหรือไม่
    $thaiID = isset($_POST['pid']) ? htmlspecialchars($_POST['pid']) : null;
    $birthdate = isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : null;
    $title = isset($_POST['titleTh']) ? htmlspecialchars($_POST['titleTh']) : null;
    $given_name = isset($_POST['given_name']) ? htmlspecialchars($_POST['given_name']) : null;
    $last_name = isset($_POST['family_name']) ? htmlspecialchars($_POST['family_name']) : null;
    $title_en = isset($_POST['title_en']) ? htmlspecialchars($_POST['title_en']) : null;
    $given_name_en = isset($_POST['given_name_en']) ? htmlspecialchars($_POST['given_name_en']) : null;
    $family_name_en = isset($_POST['family_name_en']) ? htmlspecialchars($_POST['family_name_en']) : null;

    $first_name = $title.$given_name;
    $occupation = '-';
    $phone = '00';
    $user_type = 0;
    $active = 1;
    $email = '-';
    
    /// ทดสอบแสดงข้อมูลที่รับมาจากฟอร์ม
    /*echo "PID: " . htmlspecialchars($thaiID) . "<br>";
    echo "Birthdate: " . htmlspecialchars($birthdate) . "<br>";
    echo "Title: " . htmlspecialchars($title) . "<br>";
    echo "Given Name: " . htmlspecialchars($given_name) . "<br>";
    echo "Family Name: " . htmlspecialchars($family_name) . "<br>";
    echo "Title (English): " . htmlspecialchars($title_en) . "<br>";
    echo "Given Name (English): " . htmlspecialchars($given_name_en) . "<br>";
    echo "Family Name (English): " . htmlspecialchars($family_name_en) . "<br>";*/
        
}else {
    header("Location: index.html");
}

// แสดงข้อมูลที่ได้รับ (เพื่อทดสอบ)
// var_dump($data);
// ตรวจสอบว่ามี user นี้ในระบบหรือไม่
// echo $data['pid'];
// echo $data[0]['pid'];
// echo "PID: " . $data->pid . "<br>";  // แสดง PID
// echo "ชื่อ: " . $data->given_name . "<br>";  // แสดงชื่อ (ภาษาไทย)