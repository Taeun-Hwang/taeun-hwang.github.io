# IP 평판도 조회

#!/bin/bash

# API Key 설정

API_KEY="fa6s5d1fs5ad1f3a2ds1fas53df13sad5f"

# 조회할 IP 설정

IP_TO_CHECK=$1

if [ -z "$IP_TO_CHECK" ]; then

echo "사용법: $0 <IP주소>"

exit 1

fi

# AbuseIPDB API 호출

response=$(curl -sG [https://api.abuseipdb.com/api/v2/check](https://api.abuseipdb.com/api/v2/check) \

--data-urlencode "ipAddress=$IP_TO_CHECK" \

-d maxAgeInDays=90 \

-H "Key: $API_KEY" \

-H "Accept: application/json")

# 결과 출력

if echo "$response" | grep -q "\"data\""; then

echo "AbuseIPDB 정보:"

# 특정 필드 추출 (예: abuseConfidenceScore, countryCode)

echo "$response" | grep -o '"abuseConfidenceScore":[0-9]*' | sed 's/"abuseConfidenceScore":/신뢰 점수: /'

echo "$response" | grep -o '"countryCode":"[^"]*"' | sed 's/"countryCode":/국가 코드: /'

else

echo "AbuseIPDB 조회 실패. 응답:"

echo "$response"

fi

[코드 수정](IP%20%E1%84%91%E1%85%A7%E1%86%BC%E1%84%91%E1%85%A1%E1%86%AB%E1%84%83%E1%85%A9%20%E1%84%8C%E1%85%A9%E1%84%92%E1%85%AC%20238aef0fed1380dd822ddf0b40222b32/%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20%E1%84%89%E1%85%AE%E1%84%8C%E1%85%A5%E1%86%BC%20238aef0fed138027b629c5687a3e4aa0.md)