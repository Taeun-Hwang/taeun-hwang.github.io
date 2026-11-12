<?php
// -------------------------------------------------------
// 차단 해제 요청 POST 처리 및 로그 기록
// -------------------------------------------------------
$submit_result = '';
$log_file      = '/var/log/httpd/block_requests.log';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = htmlspecialchars(trim($_POST['name']   ?? ''));
    $email  = htmlspecialchars(trim($_POST['email']  ?? ''));
    $reason = htmlspecialchars(trim($_POST['reason'] ?? ''));

    if (empty($name) || empty($email) || empty($reason)) {
        $submit_result = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $submit_result = 'invalid_email';
    } else {
        $log_entry = sprintf(
            "[%s] RequestID:%s | IP:%s | RuleID:%s | Severity:%s | Name:%s | Email:%s | Reason:%s | URI:%s | UA:%s\n",
            $request_time, $request_id, $client_ip,
            $rule_id, $audit_severity ?? 'N/A',
            $name, $email, $reason, $original_request, $user_agent
        );
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

        // 관리자 메일 발송 필요 시 주석 해제
        // $to      = 'admin@yourdomain.com';
        // $subject = "[WAF 해제 요청] {$name} / {$client_ip} / RuleID:{$rule_id}";
        // mail($to, $subject, $log_entry);

        $submit_result = 'success';
    }
}
