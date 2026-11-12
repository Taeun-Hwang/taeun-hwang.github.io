<?php
require_once __DIR__ . '/inc/env.php';
require_once __DIR__ . '/inc/lang.php';
require_once __DIR__ . '/inc/request_handler.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>접근이 차단되었습니다</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', 'Malgun Gothic', sans-serif;
    background: #f0f2f5;
    color: #333;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    max-width: 720px;
    width: 100%;
    overflow: hidden;
  }
  .header {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: white;
    padding: 36px 40px 28px;
    text-align: center;
  }
  .header .icon { font-size: 52px; margin-bottom: 12px; }
  .header h1   { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
  .header p    { font-size: 14px; opacity: 0.88; }
  .info-box {
    background: #fff8f8;
    border-left: 4px solid #e74c3c;
    margin: 28px 32px 0;
    border-radius: 0 8px 8px 0;
    padding: 18px 20px;
  }
  .info-box h3 {
    font-size: 13px;
    color: #c0392b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
  }
  .info-grid {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 7px 12px;
    font-size: 13px;
  }
  .info-grid .label { color: #888; font-weight: 600; }
  .info-grid .value {
    color: #333;
    word-break: break-all;
    font-family: monospace;
    font-size: 12.5px;
  }
  .info-grid .value.highlight {
    color: #c0392b;
    font-weight: 700;
    font-size: 13px;
    font-family: inherit;
  }
  /* Audit ID - audit 로그 조회용 */
  .info-grid .value.audit-id {
    color: #2471a3;
    font-size: 11px;
    word-break: break-all;
  }
  .tech-box {
    margin: 16px 32px 0;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    overflow: hidden;
  }
  .tech-header {
    background: #f5f5f5;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    color: #555;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
  }
  .tech-header:hover { background: #ebebeb; }
  .toggle-icon { font-size: 11px; color: #888; }
  .tech-body { display: none; padding: 16px; background: #fafafa; }
  .tech-body.open { display: block; }
  .tech-grid {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 8px 12px;
    font-size: 12.5px;
  }
  .t-label { color: #888; font-weight: 600; padding-top: 4px; }
  .t-value {
    font-family: monospace;
    color: #333;
    word-break: break-all;
    background: #f0f0f0;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    line-height: 1.5;
  }
  .t-value.na   { color: #bbb; font-style: italic; background: #f8f8f8; }
  .t-value.warn { background: #fff8f0; color: #c0392b; border: 1px solid #f5c6c6; }
  .severity-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    font-family: monospace;
    letter-spacing: 0.5px;
  }
  .tag-list { display: flex; flex-wrap: wrap; gap: 5px; }
  .tag-item {
    background: #e8f4fd;
    color: #2471a3;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-family: monospace;
  }
  .divider { border: none; border-top: 1px solid #eee; margin: 20px 32px; }
  .form-section { padding: 0 32px 32px; }
  .form-section h2 { font-size: 16px; font-weight: 700; margin-bottom: 6px; color: #222; }
  .form-section .desc { font-size: 13px; color: #666; margin-bottom: 20px; line-height: 1.6; }
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
  .form-group label .required { color: #e74c3c; margin-left: 2px; }
  .form-group input,
  .form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 7px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
    outline: none;
  }
  .form-group input:focus,
  .form-group textarea:focus { border-color: #e74c3c; }
  .form-group textarea { height: 90px; resize: vertical; }
  .hidden-info {
    background: #f8f8f8;
    border-radius: 7px;
    padding: 10px 14px;
    font-size: 12px;
    color: #888;
    margin-bottom: 18px;
    font-family: monospace;
  }
  .btn-submit {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s;
  }
  .btn-submit:hover { opacity: 0.90; }
  .alert { border-radius: 8px; padding: 14px 18px; font-size: 14px; margin-bottom: 18px; }
  .alert-success { background: #eafaf1; border-left: 4px solid #27ae60; color: #1e8449; }
  .alert-error   { background: #fff8f8; border-left: 4px solid #e74c3c; color: #c0392b; }
  .footer { text-align: center; padding: 18px; font-size: 12px; color: #aaa; border-top: 1px solid #f0f0f0; }
</style>
</head>
<body>
<div class="container">

  <div class="header">
    <div class="icon">🛡️</div>
    <h1>접근이 차단되었습니다</h1>
    <p>보안 시스템(WAF)에 의해 요청이 차단되었습니다</p>
  </div>

  <?php require __DIR__ . '/parts/block_info.php'; ?>
  <?php require __DIR__ . '/parts/tech_info.php'; ?>

  <hr class="divider">

  <?php require __DIR__ . '/parts/request_form.php'; ?>

  <div class="footer">
    © <?= date('Y') ?> Security WAF System &nbsp;|&nbsp;
    문의: admin@yourdomain.com
  </div>

</div>
<script>
function toggleTech() {
    const body = document.getElementById('tech-body');
    const icon = document.getElementById('toggle-icon');
    if (body.classList.contains('open')) {
        body.classList.remove('open');
        icon.textContent = '▼ 펼치기';
    } else {
        body.classList.add('open');
        icon.textContent = '▲ 접기';
    }
}
</script>
</body>
</html>
