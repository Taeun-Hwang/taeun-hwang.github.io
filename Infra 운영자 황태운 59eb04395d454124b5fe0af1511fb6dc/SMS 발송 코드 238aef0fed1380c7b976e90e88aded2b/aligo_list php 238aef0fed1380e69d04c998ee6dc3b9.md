# aligo_list.php

<?php
// aligo_list.php
$config = require 'config.php';

if (!empty($config['debug'])) {
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

$sms_url = "[https://apis.aligo.in/list/](https://apis.aligo.in/list/)";
$sms = [
'user_id' => $config['user_id'],
'key' => $config['api_key'],
'page' => '0',
'page_size' => '10'
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

// HTML 출력
echo "<h3>📬 최근 문자 발송 목록</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>
<th>#</th>
<th>발신번호</th>
<th>메시지</th>
<th>보낸시간</th>
<th>상태</th>
<th>상세</th>
</tr>";

foreach ($data['list'] as $index => $msg) {
echo "<tr>";
echo "<td>" . ($index + 1) . "</td>";
echo "<td>" . htmlspecialchars($msg['sender'] ?? '-') . "</td>";
echo "<td>" . nl2br(htmlspecialchars($msg['msg'] ?? '-')) . "</td>";
echo "<td>" . htmlspecialchars($msg['reg_date'] ?? '-') . "</td>";
$status = $msg['reserve_state'] ?: ((isset($msg['fail_count']) && $msg['fail_count'] > 0) ? '일부실패' : '성공');
echo "<td>" . htmlspecialchars($status) . "</td>";
echo "<td>"
. "<form method='get' action='aligo_sms_list.php' style='margin:0;'>"
. "<input type='hidden' name='mid' value='" . htmlspecialchars($msg['mid']) . "'>"
. "<button type='submit'>상세보기</button>"
. "</form>"
. "</td>";
echo "</tr>";
}
echo "</table>";