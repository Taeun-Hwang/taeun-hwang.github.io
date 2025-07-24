# 무료 인증서 발급(Let's Encrypt)

1. 패키지 설치
    #certbot : Let's Encrypt에서 무료 SSL 인증서를 발급받고 갱신하는 **기본 클라이언트**
    sudo dnf install epel-release

    #python3-certbot-nginx : certbot이 Nginx 웹 서버와 직접 연동될 수 있도록 해주는 플러그인,
    #인증서 발급 시 Nginx 설정을 자동으로 탐색하고 수정하여 SSL 설정을 적용,
    #server 블록에 ssl_certificate, ssl_certificate_key 자동 추가,
    #또한 인증서 갱신 시에도 Nginx와 연동하여 재시작이나 설정 반영이 자동 수행
    sudo dnf install certbot python3-certbot-nginx
    
    
2. 인증서 발급
    
    #Nginx 서버용 (자동 설정 포함)
    sudo certbot --nginx -d test.com -d www.test.com
    
    #Apache 서버용
    sudo certbot --apache -d test.com  -d www.test.com
    
    #웹서버 없는 환경 또는 수동 설정 시, certonly 옵션은 인증서만 발급 웹 설정은 자동 변경하지 않음
    sudo certbot certonly --standalone -d test.com  -d www.test.com
    
3. Apache  및 Nginx 적용
    
    인증서 파일 위치 /etc/letsencrypt/live/test.com/fullchain.pem,
                    /etc/letsencrypt/live/test.com/privkey.pem
    
    <Apache 예시>
    <VirtualHost *:443>
        ServerName test.com
        ServerAlias www.test.com
        DocumentRoot /var/www/html
        SSLEngine on
        SSLCertificateFile /etc/letsencrypt/live/taeun.store/fullchain.pem
        SSLCertificateKeyFile /etc/letsencrypt/live/taeun.store/privkey.pem
    
        <Directory /var/www/html>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
          </Directory>
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
    
    <Nginx 예시>
    server {
        listen 443 ssl;
        server_name test.com www.test.com;
        ssl_certificate     /etc/letsencrypt/live/taeun.store/fullchain.pem;
        ssl_certificate_key /etc/letsencrypt/live/taeun.store/privkey.pem;
        ssl_protocols       TLSv1.2 TLSv1.3;
        ssl_ciphers         HIGH:!aNULL:!MD5;
        root /var/www/html;
        index index.html index.php;
        location / {
        try_files $uri $uri/ =404;
        }
    
    }
    
5. 자동 갱신 여부 확인
    #systemd 기반 확인
    systemctl list-timers | grep certbot
    
    #실제 자동 갱신 시뮬레이트 80 포트로 테스트 함으로 apache나 nginx 는 일시 중지 해야함
    sudo certbot renew --dry-run                               
    
    #자동 갱신 제외 할려면 특정 conf 파일 제거
    sudo rm /etc/letsencrypt/renewal/test.com.conf
    
    #갱신 시도 주기 확인
    systemctl list-timers certbot-renew.timer
    
    #Certbot으로 발급받은 모든 인증서 정보(만료일) 확인
    sudo certbot certificates
    
    [root@Rocky9 ~]$  systemctl list-timers certbot-renew.timer
    
    NEXT                        LEFT     LAST                        PASSED       UNIT                ACTIVATES
    Thu 2025-04-10 04:12:29 KST 13h left Wed 2025-04-09 13:06:10 KST 1h 18min ago certbot-renew.timer certbot-renew.service
    
    NEXT :  certbot 갱신 명령이 다음에 실행될 시간
    LEFT : 지금으로부터 약 13시간 후에 실행된다는 뜻
    LAST : certbot이 마지막으로 실행된 시각
    PASSED : 마지막 실행 시각으로부터 1시간 18분 경과
    UNIT : 타이머 유닛의 이름
    ACTIVATES : 타이머가 호출하는 실제 서비스. 즉, 내부적으로 certbot renew 명령이 실행
    
    [root@Rocky9 ~]$  sudo certbot certificates
    Saving debug log to /var/log/letsencrypt/letsencrypt.log
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Found the following certs:
    Certificate Name: test.com
    Serial Number: 5aeec2cc7ae6814fd23b7f6f31152fb9b40
    Key Type: ECDSA
    Domains: test.com www.test.com
    Expiry Date: 2025-07-08 03:27:01+00:00 (VALID: 89 days)
    Certificate Path: /etc/letsencrypt/live/test.com/fullchain.pem
    Private Key Path: /etc/letsencrypt/live/test.com/privkey.pem
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
6. 와일드 카드 인증서 발급
   와일드 카드 인증서는 HTTP-01 방식으로는 검증이 불가능 하여 반드시 DNS-01 방식(TXT 레코드 인증)으로 만 가능
   sudo certbot certonly --manual \
    --preferred-challenges dns \
    -d "*.test.com" -d test.com
    
    이후 출력되는 TXT 레코드 값 등록
    
    <예시>
    [root@Rocky9 ~]$  sudo certbot certonly --manual \
    >   --preferred-challenges dns \
    >   -d "*.test.com" -d test.com
    Saving debug log to /var/log/letsencrypt/letsencrypt.log
    Requesting a certificate for *.test.com and test.com
    
    ```
        - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        Please deploy a DNS TXT record under the name:
        _acme-challenge.test.com.
        with the following value:
        dIkNWV-MD-pqiQtmNrCBlvDJrSLzzWOfJuMbGLf8KFs
        Before continuing, verify the TXT record has been deployed. Depending on the DNS
        provider, this may take some time, from a few seconds to multiple minutes. You can
        check if it has finished deploying with aid of online tools, such as the Google
        Admin Toolbox: <https://toolbox.googleapps.com/apps/dig/#TXT/_acme-challenge.taeun.store>.
        Look for one or more bolded line(s) below the line ';ANSWER'. It should show the
        value(s) you've just added.
        - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Press Enter to Continue
    
    ```
    
    위 화면에서  TXT 레코드 등록
    
    Successfully received certificate.
    Certificate is saved at: /etc/letsencrypt/live/test.com-0001/fullchain.pem
    Key is saved at:         /etc/letsencrypt/live/test.com-0001/privkey.pem
    This certificate expires on 2025-07-08.
    These files will be updated when the certificate renews.
    NEXT STEPS:
    - This certificate will not be renewed automatically. Autorenewal of --manual certificates requires the use of an authentication hook script (--manual-auth-hook) but one was not provided. To renew this certificate, repeat this same certbot command before the certificate's expiry date.
    
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
    If you like Certbot, please consider supporting our work by:
    
    * Donating to ISRG / Let's Encrypt:   [https://letsencrypt.org/donate](https://letsencrypt.org/donate)
    
    * Donating to EFF:                    [https://eff.org/donate-le](https://eff.org/donate-le)
    
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
    [root@Rocky9 ~]$
    
    가비아 DNS 레코드 등록 화면
    
    ![image.png](%E1%84%86%E1%85%AE%E1%84%85%E1%85%AD%20%E1%84%8B%E1%85%B5%E1%86%AB%E1%84%8C%E1%85%B3%E1%86%BC%E1%84%89%E1%85%A5%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%80%E1%85%B3%E1%86%B8(Let's%20Encrypt)%20238aef0fed13801792b1e1b0e82283b2/image.png)
    
7. API 키 방식
    
    DNS 레코드 등록을 API 키 방식으로 가능
    
    #다른 DNS 제공 자는 --dns-kkkdns 이런식으로 변경, certbot plugins 로 설치된 플러그 확인
    sudo certbot certonly \
     --dns-cloudflare \ 
     --dns-cloudflare-credentials /home/DNS_API.key \             #API 키파일 위치 지정
     -d "*.test.com" -d test.com
    
    #실제 작업 커맨드, 인증서 발급을 위해 DNS-01 챌린지를 시도 임시 TXT 레코드가 생성 후 없어짐, 기본은 10 초 유지후 삭제되나 너무 잛음 관계로 30초 대기후 DNS 검증 요청을 보냄
    일부 DNS 환경에서는 너무 짧으면 인증에 실패함
    sudo certbot certonly \
     --dns-cloudflare \
     --dns-cloudflare-credentials /home/taeun/.secrets/cloudflare.ini \
     --dns-cloudflare-propagation-seconds 30 \
     -d "*.test.com" -d test.com
    
    <DNS_API.key 파일 예시>
    #실제 키 값을 변경하였음
    dns_cloudflare_api_key = 4alskdnglaskdcgasldgm;salgm1   
    #cloudflare 로그인 계정
    dns_cloudflare_email = hogildong@naver.com
    
    #아래 URL에서 설치 가능한 DNS 플러그인 확인 가능
    https://pypi.org/search/?q=certbot-dns 
    
8. conf 자동 기입
    
    certonly 가 아닌 --apache 옵션을 주변 자동으로 /etc/httpd/conf 나 conf.d의  ServerName 에서 해당 도메인(test.com) 가 있는 VirtualHost 블록을 찾아 아래 인증서 부분 구문을 자동 추가됨, 하지만 소스 컴파이로 설치 할경우 링크 파일을 /etc/httpd에 만들어 주거나 apache-server-root 옵션으로 디렉토리를 지정
    
    sudo certbot --apache \
     --apache-server-root /usr/local/apache2 \
     --apache-vhost-root /usr/local/apache2/conf/extra \
    -d test.com -d www.test.com
    
    #certbot 가 자동 수정을 위한 링크파일 생성
    sudo ln -s /usr/local/apache2/bin/httpd /usr/sbin/httpd
    sudo ln -s /usr/local/apache2/bin/apachectl /usr/sbin/apachectl
    
    #certbot 가 자동 수정할 경우 기입 되는 내용 예시
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/taeun.org/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/taeun.org/privkey.pem
    
    #certbot 이 apache 데몬 인식 여부 확인 방법
    #성공적으로 VirtualHost 위치를 찾는다면 conf 자동 수정이 작동할 수 있습니다.
    
    sudo certbot --apache -d test.com -d www.test.com --dry-run
