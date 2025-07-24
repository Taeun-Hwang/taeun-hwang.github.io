# SMS 발송 코드

제조업체에 근무하던 당시, 서버 장애 발생 시 SMS 알림 체계가 마련되어 있지 않아 장애 인지 및 대응에 어려움이 있었습니다. 이에 따라, 조건에 따라 자동으로 SMS를 발송하는 시스템을 개인적으로 임시 구축하였습니다.

1. 구성도
2. 각 파일 용도(WEB에서 SMS 발송)
    
    
    | 파일 명 | 설명 |
    | --- | --- |
    | [.htaccess](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/htaccess%20238aef0fed1380af8eccef80c07d8644.md) | 기본 페이지 설정 및 HTTPS 강제 리디렉션 설정 |
    | [send_form.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/send_form%20php%20238aef0fed13807a9207d04a45a10c0a.md) | SMS 입력 폼 페이지 (메인 화면) |
    | [aligo_send.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_send%20php%20238aef0fed1380b98704deb59ed72c17.md) | 실제로 Aligo API를 통해 SMS 발송 요청 처리 |
    | [aligo_remain.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_remain%20php%20238aef0fed13800c9757dc35a74fa2a1.md) | 잔여 문자 수량 조회용 (관리 도구) |
    | [aligo_sms_list.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_sms_list%20php%20238aef0fed13804c8b22e915015e0815.md) | 발송 내역 확인용 페이지 |
    | [aligo_list.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_list%20php%20238aef0fed1380e69d04c998ee6dc3b9.md) | 등록된 메시지 템플릿 리스트 조회용 |
    | [config.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/config%20php%20238aef0fed1380dead92f64956cf4bd7.md) | Aligo API 연동용 계정 설정 포함 |
    | [navbar.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/navbar%20php%20238aef0fed1380cfb990c1a26e30fd57.md) | 공통 네비게이션 바. `.user.ini`로 자동 포함됨 |
    | [.user.ini](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/user%20ini%20238aef0fed138015a16adf7b4c3d1deb.md) | 모든 PHP 페이지에 `navbar.php` 자동 포함 |
    | [address_book.json](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/address_book%20json%20238aef0fed13805c8db7ee3d53066231.md) | 연락처 저장 JSON 파일 |
    | [address_book_edit.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/address_book_edit%20php%20238aef0fed138009baa5cbc0d5331b8b.md) | 주소록 수정 페이지 |
    
    [.htaccess](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/htaccess%20238aef0fed1380af8eccef80c07d8644.md)
    
    [.user.ini](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/user%20ini%20238aef0fed138015a16adf7b4c3d1deb.md)
    
    [send_form.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/send_form%20php%20238aef0fed13807a9207d04a45a10c0a.md)
    
    [address_book.json](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/address_book%20json%20238aef0fed13805c8db7ee3d53066231.md)
    
    [address_book_edit.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/address_book_edit%20php%20238aef0fed138009baa5cbc0d5331b8b.md)
    
    [aligo_list.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_list%20php%20238aef0fed1380e69d04c998ee6dc3b9.md)
    
    [aligo_remain.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_remain%20php%20238aef0fed13800c9757dc35a74fa2a1.md)
    
    [aligo_send.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_send%20php%20238aef0fed1380b98704deb59ed72c17.md)
    
    [aligo_sms_list.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/aligo_sms_list%20php%20238aef0fed13804c8b22e915015e0815.md)
    
    [config.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/config%20php%20238aef0fed1380dead92f64956cf4bd7.md)
    
    [navbar.php](SMS%20%E1%84%87%E1%85%A1%E1%86%AF%E1%84%89%E1%85%A9%E1%86%BC%20%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20238aef0fed1380c7b976e90e88aded2b/navbar%20php%20238aef0fed1380cfb990c1a26e30fd57.md)
    
3. 포로세스 및 포트 다운, 에러 문자 감지시 
    
    crontab에 등록하여 실행
    
    * * * * * /usr/local/bin/check_and_alert.sh
    #!/bin/bash
    📱 알림 수신자 목록 (쉼표 없이 배열로 나열)
    RECIPIENTS=("01099271179" "01012345678" "01056781234")
    📩 발신 메시지 내용
    ALERT_MSG=""
    
    1️⃣ 프로세스 확인
    PROCESS_NAME="httpd"
    if ! pgrep -x "$PROCESS_NAME" > /dev/null; then
    ALERT_MSG+="[경고] $PROCESS_NAME 프로세스가 동작하지 않습니다. "
    fi
    
    2️⃣ 포트 확인
    PORT=80
    if ! ss -ltn | grep -q ":$PORT "; then
    ALERT_MSG+="[경고] $PORT 포트가 열려있지 않습니다. "
    fi
    
    3️⃣ 로그 감시 (최근 5분 내)
    ERROR_PATTERN="Out of memory"
    if journalctl --since "5 minutes ago" | grep -q "$ERROR_PATTERN"; then
    ALERT_MSG+="[경고] 시스템 로그에서 '$ERROR_PATTERN' 탐지됨. "
    fi
    
    4️⃣ 문자 전송 요청 (수신자 반복 처리)
    if [ -n "$ALERT_MSG" ]; then
    for PHONE in "${RECIPIENTS[@]}"; do
    curl -s -X POST [http://localhost/sms/aligo_send.php](http://localhost/sms/aligo_send.php) \
    -d "receiver=$PHONE" \
    --data-urlencode "msg=$ALERT_MSG"
    done
    fi
    
    <문자 예시>
    [경고] httpd 프로세스가 동작하지 않습니다.
    
    [경고] 80 포트가 열려있지 않습니다.
    
    [경고] 시스템 로그에서 'Out of memory' 탐지됨.