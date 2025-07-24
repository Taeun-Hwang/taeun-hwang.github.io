# logrotate 설정 방법

1. /etc/logrotate.d/myapp 파일 생성
    
    /var/log/myapp.log {
    #로그를 주간 또는 일간 단위로 회전
    weekly
    #최대 N개의 로그 백업을 보관
    rotate 4
    #회전된 로그 파일을 .gz로 압축
    compress
    #회전 직후의 로그는 압축하지 않고, 다음 회전 시점에 압축, 즉, .1은 그대로 두고 .2.gz, .3.gz 부터 압축
    delaycompress
    #로그 파일이 없어도 오류 없이 넘어
    missingok
    #로그 파일이 비어 있으면 회전하지 않음
    notifempty
    #새 로그 파일을 회전 직후 자동 생성
    create 0640 root root
    postrotate
    /bin/kill -HUP $(pidof rsyslogd)
    endscript
    }
    
2. /var/log/mysql_general_log {
    
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 644 mysql mysql
    #postrotate 안에 있는 명령어가 중복 실행되지 않도록 방지
    sharedscripts
    #로그를 회전한 이후에 실행할 명령어 블럭, 기존 로그파일을 쓰고 있어서 새로운 파일에는 저장 위해
    postrotate
    mysql --defaults-file=/root/.my.cnf -e "SET GLOBAL general_log = 'OFF';"
    mysql --defaults-file=/root/.my.cnf -e "SET GLOBAL general_log_file = '/var/log/mysql_general_log';"
    mysql --defaults-file=/root/.my.cnf -e "SET GLOBAL general_log = 'ON';"
    endscript
    }
    
3. #시뮬레이션
    
    logrotate -d /etc/logrotate.d/myapp 
    
4. #강제 실행
    
    logrotate -f /etc/logrotate.d/myapp 
    
5. rsyslog 로 log 전송(실시간)
    
    #수신 활성화
    
    #UDP 수신 활성화
    module(load="imudp")
    input(type="imudp" port="514")
    
    #TCP 수신 활성화
    module(load="imtcp")
    input(type="imtcp" port="514")
    
    클라이언트 설정(로그 송신자)
    
    /etc/rsyslog.conf 수정
    
    #UDP로 전송
    **.**  @192.168.0.100:514
    
    #TCP로 전송
    **.**  @@192.168.0.100:514
    
6. 개인적인 txt 파일 로그로 전송 방법
    
    위 방법으로 rsyslog 설정을 했지만 sync가 안되는 경우 발생
    /etc/rsyslogd.conf에서 설정 추가 필요
    
    local7.* /var/log/myservice.log 
    
    또는 
    
    input(type="imfile"
    File="/home/taeun/myservice.log"
    Tag="myservice:"
    Severity="info"
    Facility="local7")
    local7.*    /var/log/myservice.log
    
    - `/home/taeun/myservice.log` 파일을 **계속 감시**
    - 새 줄이 생기면 로그처럼 처리하고, 그 로그를 `local7`으로 분류
    - `local7` 로그는 `/var/log/myservice.log`에 저장
    
    | 필드 | 설명 |
    | --- | --- |
    | type="imfile” | `rsyslog`의 `imfile` 모듈을 사용해 **일반 파일을 로그 입력으로 감시** |
    | File="/home/user/myservice.log” | 감시 대상 **로그 파일 경로** |
    | Tag="myservice:” | 로그 앞에 붙일 **식별 태그** |
    | Severity="info” | 로그의 **심각도(level)** 지정 |
    | Facility="local7” | 로그의 **분류(facility)** |