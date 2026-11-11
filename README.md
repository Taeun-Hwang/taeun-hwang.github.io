# 🛠 황태운 – 시스템 인프라 운영 관리자

Linux와 Windows Server 운영 경험을 바탕으로 시스템 안정성 향상, 보안 강화, 자동화 구축 업무를 수행하고 있습니다.  
Apache, Nginx, PHP, rsyslog, PowerShell, Bash 등을 활용한 실전 구축·장애 대응 경험을 정리한 포트폴리오입니다.

---

## 🌐 사이트

- [메인 페이지](https://taeun-hwang.github.io/)
- [스킬 상세](./skill.html)
- [대표 프로젝트](./projects.html)

---

## 🧑‍💻 운영 분야 스킬셋

### OS & 시스템 관리
- **Linux**: Rocky 9, Ubuntu, RHEL, CentOS
- **Windows Server**: 2016, 2019, AD/Group Policy
- WSL 2, WinPE, DISM, 레지스트리 자동화
- KVM/libvirt, OpenStack 환경 VM 운영

### 웹 & 네트워크 서비스
- Apache, Nginx, PHP, MariaDB, SSL 인증서 구성
- 리버스 프록시(SSL 종료), 가상호스트
- Let’s Encrypt (HTTP-01 / DNS-01), Cloudflare API DNS 자동화

### 보안 점검·하드닝 & 모니터링
- PAM 패스워드 정책, 로그 파일 권한 최소화
- RDP 세션 타임아웃 (레지스트리/GPO)
- rsyslog, logrotate
- RDP 접근 감지 후 문자 알림 (PowerShell)
- ModSecurity for IIS / Apache
- AbuseIPDB 기반 악성 IP 차단

### 자동화
- Bash: 백업, 로그 순환, 서비스·포트 체크
- PowerShell: 이벤트 감시, 시간 동기화
- rsync, inotify, systemd 타이머, crontab

---

## 📄 이력서

- [이력서 PDF 다운로드](./assets/resume.pdf) (작성 중)

---

## 📦 대표 프로젝트

| 프로젝트 | 설명 | 상세 |
|----------|------|------|
| AbuseIPDB 기반 Apache 자동 차단 | PHP + API로 악성 IP 수집 후 Apache 2.2/2.4 규칙 자동 갱신 | [보기](./projects/abuseipdb-blocker/) |
| Linux 서버 장애 감지 및 알림 | 리소스·프로세스·포트 점검 후 Slack/SMS 알림 | [보기](./projects/linux-monitoring/) |
| Let’s Encrypt 인증서 자동화 | certbot DNS-01 + Cloudflare API로 와일드카드 발급·갱신 | [보기](./projects/letsencrypt/) |
| SSL 리버스 프록시 | Nginx/Apache에서 SSL 종료 후 백엔드 단순화 | [보기](./projects/ssl-proxy/) |
| SSSD + LDAP/AD + RADIUS 중앙 인증 | 서버별 로컬 계정 대신 중앙 계정으로 SSH 통합 | [보기](./projects/central-auth/) |

---

## 📂 저장소 구조

```text
├── index.html
├── skill.html
├── projects.html
├── assets/
│   └── resume.pdf
└── projects/
    ├── abuseipdb-blocker/
    │   ├── index.html
    │   ├── images/
    │   └── code/
    ├── linux-monitoring/
    │   ├── index.html
    │   ├── images/
    │   └── code/
    └── ...