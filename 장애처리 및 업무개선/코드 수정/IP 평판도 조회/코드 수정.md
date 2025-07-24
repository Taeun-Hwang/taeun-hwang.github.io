# 코드 수정

사유 : 조회 시 국가 ISP 등 정보 표시 추가

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

echo "$response" | grep -o '"abuseConfidenceScore":[0-9]*' | sed 's/"abuseConfidenceScore":/신뢰 점수: /'

echo "$response" | grep -o '"countryCode":"[^"]*"' | sed 's/"countryCode":/국가 코드: /'

echo "$response" | grep -o '"isp":"[^"]*"' | sed 's/"isp":/ISP: /'

else

echo "AbuseIPDB 조회 실패. 응답:"

echo "$response"

fi