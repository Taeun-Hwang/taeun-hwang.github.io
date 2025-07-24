# aligo_remain.php

<?php
// 설정 파일 불러오기
$config = require 'config.php';

// 디버그 모드 적용
if (!empty($config['debug'])) {
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

// Aligo API 요청 URL
$sms_url = "[https://apis.aligo.in/remain/](https://apis.aligo.in/remain/)";

// API 요청 데이터
$sms = [
'user_id' => $config['user_id'],
'key' => $config['api_key']
];

// CURL 설정 및 실행
$port = (parse_url($sms_url, PHP_URL_SCHEME) === 'https') ? 443 : 80;

$ch = curl_init();
curl_setopt($ch, CURLOPT_PORT, $port);
curl_setopt($ch, CURLOPT_URL, $sms_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

// JSON 디코딩
$data = json_decode($response, true);

// 결과 확인
echo "<h3>📦 전송 가능 건수 확인</h3>";

if ($data === null || !isset($data['result_code'])) {
echo "<strong>❌ 응답 파싱 실패</strong><br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";
exit;
}

if ($data['result_code'] != 1) {
echo "<strong>❌ 오류:</strong> " . htmlspecialchars($data['message']);
exit;
}

// HTML 테이블 출력
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>유형</th><th>잔여 건수</th></tr>";
echo "<tr><td>SMS</td><td>" . htmlspecialchars($data['SMS_CNT'] ?? '-') . "</td></tr>";
echo "<tr><td>LMS</td><td>" . htmlspecialchars($data['LMS_CNT'] ?? '-') . "</td></tr>";
echo "<tr><td>MMS</td><td>" . htmlspecialchars($data['MMS_CNT'] ?? '-') . "</td></tr>";
echo "</table>";