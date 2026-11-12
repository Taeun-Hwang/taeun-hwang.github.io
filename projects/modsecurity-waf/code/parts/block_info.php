<!-- 차단 상세 정보 -->
<div class="info-box">
  <h3>🔍 차단 상세 정보</h3>
  <div class="info-grid">

    <span class="label">차단 사유</span>
    <span class="value highlight"><?= htmlspecialchars($block_msg_display) ?></span>

    <span class="label">룰 ID</span>
    <span class="value"><?= htmlspecialchars($rule_id) ?></span>

    <span class="label">요청 URL</span>
    <span class="value"><?= htmlspecialchars($original_uri) ?></span>

    <span class="label">클라이언트 IP</span>
    <span class="value"><?= htmlspecialchars($client_ip) ?></span>

    <span class="label">요청 시각</span>
    <span class="value"><?= $request_time ?></span>

    <span class="label">요청 ID</span>
    <span class="value"><?= $request_id ?></span>

    <?php if ($unique_id): ?>
    <span class="label">Audit ID</span>
    <span class="value audit-id"><?= htmlspecialchars($unique_id) ?></span>
    <?php endif; ?>

  </div>
</div>
