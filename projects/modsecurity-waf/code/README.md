# WAF 차단 안내 페이지

## 전체 구조

/etc/httpd/
├── conf.d/
│   ├── waf-base.conf                     # WAF ErrorDocument 및 디렉토리 접근 설정
│   └── mod_security.conf                 # ModSecurity 엔진 설정
└── modsecurity.d/
    ├── crs-setup.conf                    # OWASP CRS 전역 설정
    ├── activated_rules/                  # OWASP 기본 룰셋
    └── custom-rules/
        └── 00-block-info.conf            # 룰 ID별 환경변수 설정

/var/www/html/waf/
├── README.md                             # 이 파일
├── block.php                             # 진입점: include 조합 및 HTML 뼈대
├── inc/
│   ├── env.php                           # 환경변수 수집 및 audit 로그 파싱
│   ├── lang.php                          # 영문 코드 → 한글 메시지 매핑
│   └── request_handler.php              # POST 해제 요청 처리 및 로그 기록
└── parts/
    ├── block_info.php                    # [차단 상세 정보] 섹션
    ├── tech_info.php                     # [상세 기술 정보] 섹션
    └── request_form.php                  # [차단 해제 요청] 섹션

/var/log/httpd/
├── modsec_audit.log                      # ModSecurity 감사 로그
├── modsec_debug.log                      # ModSecurity 디버그 로그
└── block_requests.log                    # 차단 해제 요청 접수 로그

## 파일별 용도

| 파일 | 용도 |
|------|------|
| waf-base.conf | ErrorDocument 403 설정, /var/www/html/waf 접근 허용 |
| mod_security.conf | SecRuleEngine, SecDefaultAction, 룰 Include 순서 관리 |
| 00-block-info.conf | 룰 ID별 setenv로 차단 사유 환경변수 설정 |
| block.php | 진입점. inc/parts를 include하여 페이지 조합 |
| inc/env.php | REDIRECT_* 환경변수 수집, audit 로그 파싱 |
| inc/lang.php | 영문 setenv 값을 한글 메시지로 변환 |
| inc/request_handler.php | 해제 요청 POST 유효성 검사 및 로그 기록 |
| parts/block_info.php | 차단 사유, 룰 ID, IP, 시각 표시 |
| parts/tech_info.php | 요청 문자열, 원문 메시지, 심각도, 태그 표시 |
| parts/request_form.php | 이름/이메일/사유 입력 폼 |

## 요청 흐름

1. 클라이언트 요청
2. ModSecurity 룰 매칭
3. 차단 → SecDefaultAction (status:403 + setenv)
4. Apache ErrorDocument 403 → /waf/block.php
5. block.php → inc/env.php (환경변수 + audit 파싱)
6. block.php → inc/lang.php (한글 변환)
7. block.php → inc/request_handler.php (POST 처리)
8. block.php → parts/*.php (화면 렌더링)
9. 해제 요청 제출 → block_requests.log 기록

## 주요 설정값

| 항목 | 값 |
|------|-----|
| 차단 페이지 경로 | /var/www/html/waf/block.php |
| 해제 요청 로그 | /var/log/httpd/block_requests.log |
| audit 로그 | /var/log/httpd/modsec_audit.log |
| 커스텀 룰 경로 | /etc/httpd/modsecurity.d/custom-rules/ |
