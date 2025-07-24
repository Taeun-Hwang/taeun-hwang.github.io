# SSL Proxy

SSL/TLS 암호화 통신을 통해 사용자 데이터 보호와 신뢰성 향상을 주면서 실제 웹 서버 부하를 줄이고, 맬웨어 탐지 및 통합 인증서 관리를 통한 보안 운영 효율성을 강화 및 인증서 통합 관리 방안를 위한 SSL Proxy 설정 방법

- 구성
**[클라이언트]** --(HTTPS)--> **[SSL Proxy Server]** --(HTTP or 재암호화 HTTPS)--> **[웹 서버]**
- SSL Proxy 서버 설정(ngnix)
    
    sudo dnf install nginx            #Proxy 설정을 위한 nginx 설치
    
    vim /etc/nginx/conf.d/test.com.conf
    
    cat /etc/nginx/conf.d/test.com.conf
    
    server {
    
    listen 443 ssl;
    server_name test.com;
    ssl_certificate    /etc/letsencrypt/live/taeun.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/taeun.org/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    location / {
    proxy_pass         [http://1](http://211.40.221.110/)92.168.1.100;     #실제 웹서버 입력, Proxy와 web 서버는 http로 통신하기 때문에 실제는 사설망 사용 권장
    proxy_set_header   Host $host;
    proxy_set_header   X-Real-IP $remote_addr;
    proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header   X-Forwarded-Proto $scheme;
    }
    
    }
    
    # HTTP to HTTPS 리디렉션 설정
    
    server {
    
    listen 80;
    server_name test.com;
    return 301 [https://$host$request_uri](https://$host$request_uri/);
    
    }
    
- SSL Proxy 서버 설정(apache)
    
    # SSL 모듈
    LoadModule ssl_module modules/mod_ssl.so
    
    # Proxy 관련 모듈
    LoadModule proxy_module modules/mod_proxy.so
    LoadModule proxy_http_module modules/mod_proxy_http.so
    
    # Rewrite (리디렉션) 모듈
    LoadModule rewrite_module modules/mod_rewrite.so
    
    cat /etc/httpd/conf.d/test.com.conf
    
    # 80포트: HTTP 요청을 443 HTTPS로 리디렉션
    
    <VirtualHost *:80>
    
    ServerName test.com
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^/(.*)$ [https://%{HTTP_HOST}/$1](https://%25%7bHTTP_HOST%7d/$1) [R=301,L]
    
    </VirtualHost>
    
    # 443포트: SSL 프록시 서버
    
    <VirtualHost *:443>
    
    ServerName test.com
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/taeun.org/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/taeun.org/privkey.pem
    SSLProtocol all -SSLv2 -SSLv3
    SSLCipherSuite HIGH:!aNULL:!MD5
    ProxyPreserveHost On
    ProxyPass / [http://192.168.1.100/](http://211.40.221.110/)      #실제 WEB 서버와 연결, 연결은 HTTP 사용
    ProxyPassReverse / [http://192.168.1.100/](http://211.40.221.110/)
    RequestHeader set X-Forwarded-Proto "https"
    RequestHeader set X-Forwarded-For %{REMOTE_ADDR}s
    
    </VirtualHost>