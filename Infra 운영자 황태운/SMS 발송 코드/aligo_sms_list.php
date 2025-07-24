# aligo_sms_list.php

<?php
$config = require 'config.php';

if (!empty($config['debug'])) {
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

$mid = $_GET['mid'] ?? '';
if (empty($mid)) {
echo "❌ 메시지 ID(mid)가 지정되지 않았습니다.";
exit;
}

$sms_url = "[https://apis.aligo.in/sms_list/](https://apis.aligo.in/sms_list/)";
$sms = [
'user_id' => $config['user_id'],
'key' => $config['api_key'],
'mid' => $mid,
'page' => '1',
'page_size' => '100'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sms_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data === null || !isset($data['list'])) {
echo "<strong>❌ 응답 파싱 실패 또는 결과 없음</strong><br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";
exit;
}

echo "<h3>📨 메시지 ID: $mid 의 수신자별 상세 내역</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>
<th>#</th>
<th>발신번호</th>
<th>수신번호</th>
<th>타입</th>
<th>상태</th>
<th>요청시간</th>
<th>발송시간</th>
<th>예약시간</th>
</tr>";

foreach ($data['list'] as $i => $msg) {
echo "<tr>";
echo "<td>" . ($i + 1) . "</td>";
echo "<td>" . htmlspecialchars($msg['sender'] ?? '-') . "</td>";
echo "<td>" . htmlspecialchars($msg['receiver'] ?? '-') . "</td>";
echo "<td>" . htmlspecialchars($msg['type'] ?? '-') . "</td>";
echo "<td>" . htmlspecialchars($msg['sms_state'] ?? '-') . "</td>";
echo "<td>" . htmlspecialchars($msg['reg_date'] ?? '-') . "</td>";
echo "<td>" . htmlspecialchars($msg['send_date'] ?? '-') . "</td>";
echo "<td>" . (!empty($msg['reserve_date']) ? htmlspecialchars($msg['reserve_date']) : '-') . "</td>";
echo "</tr>";
}

echo "</table>";