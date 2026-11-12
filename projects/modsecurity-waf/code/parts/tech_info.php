<!-- 상세 기술 정보 (접기/펼치기) -->
<div class="tech-box">
  <div class="tech-header" onclick="toggleTech()">
    <span>🔧 상세 기술 정보 (관리자/개발자용)</span>
    <span class="toggle-icon" id="toggle-icon">▼ 펼치기</span>
  </div>
  <div class="tech-body" id="tech-body">
    <div class="tech-grid">

      <span class="t-label">요청 문자열</span>
      <span class="t-value warn">
        <?php
          $display = $original_request;
          if ($original_method === 'POST' && !empty($post_body)) {
              $display .= "\nBody: " . $post_body;
          }
          echo htmlspecialchars($display);
        ?>
      </span>

      <span class="t-label">요청 메서드</span>
      <span class="t-value"><?= htmlspecialchars($original_method) ?></span>

      <span class="t-label">원문 메시지</span>
      <span class="t-value <?= $audit_msg ? '' : 'na' ?>">
        <?= $audit_msg ? htmlspecialchars($audit_msg) : 'N/A' ?>
      </span>

      <span class="t-label">매칭 데이터</span>
      <span class="t-value <?= $audit_data ? '' : 'na' ?>">
        <?= $audit_data ? htmlspecialchars($audit_data) : 'N/A' ?>
      </span>

      <span class="t-label">심각도</span>
      <span class="t-value">
        <?php if ($audit_severity): ?>
          <span class="severity-badge"
                style="background:<?= $sev_info['bg'] ?>;color:<?= $sev_info['color'] ?>">
            <?= htmlspecialchars($sev_info['label']) ?>
          </span>
        <?php else: ?>
          <span class="na">N/A</span>
        <?php endif; ?>
      </span>

      <span class="t-label">User-Agent</span>
      <span class="t-value <?= ($user_agent !== 'Unknown') ? '' : 'na' ?>">
        <?= htmlspecialchars($user_agent) ?>
      </span>

      <?php if (!empty($audit_tags)): ?>
      <span class="t-label">분류 태그</span>
      <span class="t-value" style="background:transparent;padding:0;">
        <div class="tag-list">
          <?php foreach ($audit_tags as $tag): ?>
            <span class="tag-item"><?= htmlspecialchars($tag) ?></span>
          <?php endforeach; ?>
        </div>
      </span>
      <?php endif; ?>

    </div>
  </div>
</div>
