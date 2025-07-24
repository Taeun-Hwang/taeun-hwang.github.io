# aligo_send.php

<?php
session_start();                                // ★추가 맨 위

// ★ 중복·위조 토큰이면 send_form.php 로 돌려보냄
if (
empty($_POST['sms_token']) ||
empty($_SESSION['sms_token']) ||
!hash_equals($_SESSION['sms_token'], $_POST['sms_token'])
) {
header('HTTP/1.1 303 See Other');                // 303: “다른 URL로 GET 하세요”
header('Location: send_form.php');               // ← 목적지
exit;                                            // 추가 ✔
}
unset($_SESSION['sms_token']);                       // 정상 토큰은 소멸
// ────────────────────────────────────────────────

$config = require 'config.php';

if (!empty($config['debug'])) {
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

// API 설정
$sms_url = "[https://apis.aligo.in/send/](https://apis.aligo.in/send/)";
$sms = [
'user_id' => $config['user_id'],
'key' => $config['api_key'],
'msg' => stripslashes($_POST['msg'] ?? '%고객명%님. 안녕하세요. API TEST SEND'),
'receiver' => $_POST['receiver'] ?? '01011112222,01033334444',
'destination' => $_POST['destination'] ?? '01011112222|김테스트,01033334444|홍예제',
'sender' => $_POST['sender'] ?? $config['default_sender'],
'rdate' => $_POST['rdate'] ?? '',
'rtime' => $_POST['rtime'] ?? '',
'testmode_yn' => $_POST['testmode_yn'] ?? 'Y',
'title' => $_POST['subject'] ?? '테스트 제목',
'msg_type' => $_POST['msg_type'] ?? 'SMS',
];

// 이미지 전송 처리
if (!empty($_FILES['image']['tmp_name'])) {
$tmp_filetype = mime_content_type($*FILES['image']['tmp_name']);
if (in_array($tmp_filetype, ['image/png', 'image/jpg', 'image/jpeg'])) {
$save_path = './uploads/' . uniqid() . '*' . basename($_FILES['image']['name']);
if (move_uploaded_file($_FILES['image']['tmp_name'], $save_path)) {
$filename = basename($save_path);
if (version_compare(PHP_VERSION, '5.5') >= 0) {
$sms['image'] = new CURLFile($save_path, $tmp_filetype, $filename);
} else {
$sms['image'] = '@' . $save_path . ';filename=' . $filename . ';type=' . $tmp_filetype;
}
}
}
}

// cURL 요청
$host_info = explode("/", $sms_url);
$port = $host_info[0] == 'https:' ? 443 : 80;

$ch = curl_init();
curl_setopt($ch, CURLOPT_PORT, $port);
curl_setopt($ch, CURLOPT_URL, $sms_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $sms);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

// 응답 파싱
$data = json_decode($response, true);

echo "<h3>📤 문자 전송 결과</h3>";
if ($data === null || !isset($data['result_code'])) {
echo "<strong>❌ 응답 파싱 실패</strong><br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";
exit;
}

// HTML 테이블 출력
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>결과 코드</th><td>" . htmlspecialchars($data['result_code']) . "</td></tr>";
echo "<tr><th>메시지</th><td>" . htmlspecialchars($data['message']) . "</td></tr>";
echo "<tr><th>성공 건수</th><td>" . htmlspecialchars($data['success_cnt'] ?? '-') . "</td></tr>";
echo "<tr><th>에러 건수</th><td>" . htmlspecialchars($data['error_cnt'] ?? '-') . "</td></tr>";
echo "<tr><th>전송 타입</th><td>" . htmlspecialchars($data['msg_type'] ?? '-') . "</td></tr>";
echo "<tr><th>메시지 ID</th><td>" . htmlspecialchars($data['msg_id'] ?? '-') . "</td></tr>";
echo "</table>";