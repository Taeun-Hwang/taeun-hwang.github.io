<?php
// -------------------------------------------------------
// 영문 코드 → 한글 메시지 매핑
// -------------------------------------------------------
$msg_map = [
    'XSS_Attack_Detected'        => 'XSS(크로스사이트 스크립팅) 공격 패턴이 감지되었습니다',
    'SQL_Injection_Detected'     => 'SQL Injection 공격 패턴이 감지되었습니다',
    'Path_Traversal_Detected'    => '경로 탐색(Path Traversal) 시도가 감지되었습니다',
    'RCE_Attempt_Detected'       => '원격 명령 실행(RCE) 패턴이 감지되었습니다',
    'Malicious_Scanner_Detected' => '악성 스캐너/봇으로 등록된 User-Agent입니다',
    '보안룰에의해차단됨'           => '보안 정책에 의해 차단되었습니다',
    'unknown'                    => '보안 정책에 의해 차단되었습니다',
];

$block_msg_display = $msg_map[$block_msg] ?? $block_msg;
