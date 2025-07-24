# Linux 장애 및 에러 종합

1. chmod 777 /etc
    
    OS가 최신일 경우 복구 가능
    
    #권한 문제가 있는 파일은 출력에서 . 대신 M이 표시됩니다.
    rpm -Va --nofiledigest   
    
    #시스템(/etc)에 설치된 모든 패키지의 파일 권한을 기본값으로 복구
    rpm --setperms -a   
    
2. SSL 인증서 에러(non-www 지원)
인증서 발급시 www로 발급, 
www.test.com 이 아닌 test.com으로 발급시 www로 접속하면 사파리 브라우저에서  [신뢰할 수 없는 인증서] 에러발생
3. Read Only 장애
    
    특정 파일시스템 /mbox Read Only 발생
    
    콘솔 연결시 아래와 같음
    
    ![image.png](Linux%20%E1%84%8C%E1%85%A1%E1%86%BC%E1%84%8B%E1%85%A2%20%E1%84%86%E1%85%B5%E1%86%BE%20%E1%84%8B%E1%85%A6%E1%84%85%E1%85%A5%20%E1%84%8C%E1%85%A9%E1%86%BC%E1%84%92%E1%85%A1%E1%86%B8%20238aef0fed138000b83bcfbdd8df8bb6/image.png)
    
    패스워드 입력하여 로그인후
    
    e2fsck -vf /dev/sda2 로 복구 시도 하였지만 [Error reading block 356816151…..] 이 계속 발생하여 fstab에서 주석 처리 시도
    
    /etc/fstab가 리드 온리로 열려 mount -o remount, rw / 로 변경 읽기와 쓰기로 /를 재마운트
    
4. OpenStact 오픈스택 CLI로 VM 재부팅
    
    #com에서 VM 전체 상태 확인
    virsh list --all	
    
    [root@hstack-com03 ~]# virsh list --all
    Id Name State
    
    7     instance-0000011f              running
    10    instance-000001a9              running
    16    instance-0000024b              running
    22    instance-000002db              running
    29    instance-000002c9              running
    30    instance-000001ac              running
    31    instance-000001a6              running
    
    - `instance-00000041 shut off`
    - `instance-000000ce shut off`
    - `instance-000001be shut off`
    - `instance-000001c1 shut off`
    
    #UUID 확인 실제 각 VM 전체에서 확인
    virsh domuuid instance-000002c9	
    20e398ec-f21b-4869-9b6a-70fecf546ae5
    
    #네트워크 인터페이스 mac 확인
    virsh domiflist instance-0000011f	
    Interface Type Source Model MAC
    
    tape0a729d9-4a bridge     qbre0a729d9-4a virtio      fa:16:3e:b7:33:bb
    tap50cf6054-ad bridge     qbr50cf6054-ad virtio      fa:16:3e:8c:86:fc
    tap855c9665-86 bridge     qbr855c9665-86 virtio      fa:16:3e:5b:0d:a8
    
    #강제 재실행 instance-000002c9
    virsh reset instance-000002c9		
    
    #소프트 재실행 instance-000002c9
    virsh reboot instance-000002c9		
    
    #강제 종료 instance-000002c9
    virsh destroy instance-000002c9	
    
    #실행 instance-000002c9	
    virsh start instance-000002c9	
    	
    
    콘솔 접속 방법
    virsh console <도메인이름 또는 ID>
    virsh console instance-000002c9
    
    ![image.png](Linux%20%E1%84%8C%E1%85%A1%E1%86%BC%E1%84%8B%E1%85%A2%20%E1%84%86%E1%85%B5%E1%86%BE%20%E1%84%8B%E1%85%A6%E1%84%85%E1%85%A5%20%E1%84%8C%E1%85%A9%E1%86%BC%E1%84%92%E1%85%A1%E1%86%B8%20238aef0fed138000b83bcfbdd8df8bb6/image%201.png)
    
    실제 접속 화면
    
    ![image.png](Linux%20%E1%84%8C%E1%85%A1%E1%86%BC%E1%84%8B%E1%85%A2%20%E1%84%86%E1%85%B5%E1%86%BE%20%E1%84%8B%E1%85%A6%E1%84%85%E1%85%A5%20%E1%84%8C%E1%85%A9%E1%86%BC%E1%84%92%E1%85%A1%E1%86%B8%20238aef0fed138000b83bcfbdd8df8bb6/image%202.png)