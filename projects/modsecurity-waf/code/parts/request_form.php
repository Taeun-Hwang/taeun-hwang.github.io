<!-- 차단 해제 요청 폼 -->
<div class="form-section">
  <h2>📋 차단 해제 요청</h2>
  <p class="desc">
    정상적인 접근이 차단된 경우 아래 양식을 작성하면 관리자가 검토 후
    안내해드립니다. <strong>요청 ID</strong>를 함께 기재해 두면
    빠른 처리에 도움이 됩니다.
  </p>

  <?php if ($submit_result === 'success'): ?>
    <div class="alert alert-success">
      ✅ 해제 요청이 접수되었습니다. 관리자 검토 후 이메일로 안내드립니다.<br>
      <strong>요청 ID: <?= $request_id ?></strong>
    </div>

  <?php elseif ($submit_result === 'error'): ?>
    <div class="alert alert-error">
      ⚠️ 모든 항목을 입력해주세요.
    </div>

  <?php elseif ($submit_result === 'invalid_email'): ?>
    <div class="alert alert-error">
      ⚠️ 올바른 이메일 주소를 입력해주세요.
    </div>
  <?php endif; ?>

  <?php if ($submit_result !== 'success'): ?>
  <form method="POST" action="/waf/block.php">
    <div class="hidden-info">
      📌 요청 ID: <strong><?= $request_id ?></strong>
      &nbsp;|&nbsp; 룰 ID: <strong><?= htmlspecialchars($rule_id) ?></strong>
      &nbsp;|&nbsp; IP: <strong><?= htmlspecialchars($client_ip) ?></strong>
    </div>

    <input type="hidden" name="request_id"  value="<?= $request_id ?>">
    <input type="hidden" name="rule_id"     value="<?= htmlspecialchars($rule_id) ?>">
    <input type="hidden" name="client_ip"   value="<?= htmlspecialchars($client_ip) ?>">
    <input type="hidden" name="block_msg"   value="<?= htmlspecialchars($block_msg_display) ?>">
    <input type="hidden" name="request_uri" value="<?= htmlspecialchars($original_request) ?>">

    <div class="form-group">
      <label>이름 <span class="required">*</span></label>
      <input type="text" name="name" placeholder="홍길동"
             value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>이메일 <span class="required">*</span></label>
      <input type="email" name="email" placeholder="you@example.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>차단 해제 요청 사유 <span class="required">*</span></label>
      <textarea name="reason"
                placeholder="어떤 작업을 하던 중 차단되었는지 설명해주세요"><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn-submit">🔓 차단 해제 요청 보내기</button>
  </form>
  <?php endif; ?>
</div>
