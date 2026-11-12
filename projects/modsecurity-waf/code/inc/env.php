<?php
// -------------------------------------------------------
// 환경변수 수집 및 audit 로그 파싱
// -------------------------------------------------------

// 기본 요청 정보
$client_ip       = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$request_time    = date('Y-m-d H:i:s');
$request_id      = uniqid('REQ-', true);
$user_agent      = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$original_uri    = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?? '/';
$original_method = $_SERVER['REDIRECT_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET';
$original_query  = $_SERVER['REDIRECT_QUERY_STRING'] ?? $_SERVER['QUERY_STRING'] ?? '';
$post_body       = $_SERVER['REDIRECT_REQUEST_BODY'] ?? null;

// Apache unique_id (ModSecurity audit 로그와 연결되는 실제 ID)
$unique_id = $_SERVER['REDIRECT_UNIQUE_ID'] ?? $_SERVER['UNIQUE_ID'] ?? null;

// 원래 요청 전체 문자열 조합
$original_request = $original_method . ' ' . $original_uri;
if (!empty($original_query)) {
    $original_request = $original_method . ' ' . $original_uri . '?' . $original_query;
}

// ModSecurity 환경변수
$rule_id   = $_SERVER['REDIRECT_MODSEC_BLOCK_RULE'] ?? $_SERVER['MODSEC_BLOCK_RULE'] ?? null;
$block_msg = $_SERVER['REDIRECT_MODSEC_BLOCK_MSG']  ?? $_SERVER['MODSEC_BLOCK_MSG']  ?? null;

// -------------------------------------------------------
// audit 로그 파싱 - 룰 ID가 포함된 가장 최근 블록 추출
// -------------------------------------------------------
$audit_rule_id  = null;
$audit_msg      = null;
$audit_data     = null;
$audit_severity = null;
$audit_tags     = [];

$audit_log = '/var/log/httpd/modsec_audit.log';
if (is_readable($audit_log)) {
    $raw = shell_exec("tail -500 " . escapeshellarg($audit_log));
    if ($raw) {
        $blocks = preg_split('/--[a-f0-9]+-Z--/', $raw);
        $best_block = null;
        foreach (array_reverse($blocks) as $block) {
            if (strpos($block, '[id "') !== false) {
                $best_block = $block;
                break;
            }
        }
        if ($best_block) {
            preg_match('/\[id "(\d+)"\]/',         $best_block, $id_m);
            preg_match('/\[msg "([^"]+)"\]/',      $best_block, $msg_m);
            preg_match('/\[data "([^"]+)"\]/',     $best_block, $data_m);
            preg_match('/\[severity "([^"]+)"\]/', $best_block, $sev_m);
            preg_match_all('/\[tag "([^"]+)"\]/',  $best_block, $tag_m);

            $audit_rule_id  = $id_m[1]   ?? null;
            $audit_msg      = $msg_m[1]  ?? null;
            $audit_data     = $data_m[1] ?? null;
            $audit_severity = $sev_m[1]  ?? null;
            if (!empty($tag_m[1])) {
                $audit_tags = array_unique($tag_m[1]);
            }
        }
    }
}

// 환경변수 없으면 audit 파싱값 사용
if (empty($rule_id) || $rule_id === 'N/A') {
    $rule_id = $audit_rule_id ?: 'N/A';
}
$rule_id   = $rule_id   ?: 'N/A';
$block_msg = $block_msg ?: 'unknown';

// 심각도 배지 스타일
$severity_map = [
    'CRITICAL' => ['label' => 'CRITICAL', 'color' => '#c0392b', 'bg' => '#fff0f0'],
    'ERROR'    => ['label' => 'ERROR',    'color' => '#e67e22', 'bg' => '#fff8f0'],
    'WARNING'  => ['label' => 'WARNING',  'color' => '#f39c12', 'bg' => '#fffbf0'],
    'NOTICE'   => ['label' => 'NOTICE',   'color' => '#2980b9', 'bg' => '#f0f8ff'],
];
$sev_info = $severity_map[$audit_severity]
          ?? ['label' => $audit_severity ?? 'N/A', 'color' => '#888', 'bg' => '#f8f8f8'];
