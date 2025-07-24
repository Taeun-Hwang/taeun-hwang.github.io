# Apache 에서 평판도 기반 IP 차단

공격의심(평판도 낮은) IP 차단 철차

- apache 2.2 이하 : block_ips_deny.conf
- apache 2.4 이상 : block_ips_require.conf

httpd.conf 에서 마지막에 Include /etc/httpd/conf.d/block_ips_deny.conf 추가후 apache 재기동

추후 crontab으로 주 1회 자동 갱신 설정 예정

###### abuseipdb.com 에서 의심 IP Export 및 apache 버전별 파일 생성 스크립트 ######

#!/bin/bash

# 기본 설정

BASE_DIR="/home/myscript/ipblock"
# AbuseIPDB에서 다운로드한 JSON 파일 경로
JSON_FILE="$BASE_DIR/blacklist.json"

# Apache 2.2용 차단 규칙 파일 경로

APACHE_BLOCK_DENY_FILE="$BASE_DIR/block_ips_deny.conf"    

# Apache 2.4용 차단 규칙 파일 경로

APACHE_BLOCK_REQUIRE_FILE="$BASE_DIR/block_ips_require.conf"

# AbuseIPDB API 키 하루 5번 사용 가능

API_KEY="fa6s5d1fs5ad1f3a2ds1fas53df13sad5f" 

# 1. 디렉토리 확인 및 생성

if [ ! -d "$BASE_DIR" ]; then

echo "Creating directory: $BASE_DIR"

mkdir -p "$BASE_DIR"

fi

# 2. AbuseIPDB에서 JSON 파일 다운로드

echo "Downloading blacklist from AbuseIPDB..."

curl -G -k [https://api.abuseipdb.com/api/v2/blacklist](https://api.abuseipdb.com/api/v2/blacklist) \

--data-urlencode "confidenceMinimum=90" \

--data-urlencode "maxAgeInDays=30" \

-H "Key: $API_KEY" \

-H "Accept: application/json" -o "$JSON_FILE"

# 다운로드 실패 시 종료

if [ $? -ne 0 ] || [ ! -s "$JSON_FILE" ]; then

echo "Error: Failed to download blacklist or empty file."

exit 1

fi

# 3. Apache 2.2용 Deny from 규칙 초기화 + <Location> 블록 시작

echo "# Auto-generated blacklist for Apache 2.2" > "$APACHE_BLOCK_DENY_FILE"

echo "# Last updated: $(date)" >> "$APACHE_BLOCK_DENY_FILE"

cat <<EOF >> "$APACHE_BLOCK_DENY_FILE"

<Location "/">

Order allow,deny

EOF

# 4. Apache 2.4용 Require not ip 규칙 초기화 + <Location> 블록 시작

echo "# Auto-generated blacklist for Apache 2.4" > "$APACHE_BLOCK_REQUIRE_FILE"

echo "# Last updated: $(date)" >> "$APACHE_BLOCK_REQUIRE_FILE"

cat <<EOF >> "$APACHE_BLOCK_REQUIRE_FILE"

<Location "/">

<RequireAll>

EOF

# 5. 국가별로 IP 정리 및 규칙 생성

echo "Generating Apache block rules grouped by country..."

cat "$JSON_FILE" | jq -r '.data[] | "\(.countryCode) \(.ipAddress)"' | sort | while read -r country ip; do

# Apache 2.2용: Deny from 규칙

if ! grep -q "######### $country #########" "$APACHE_BLOCK_DENY_FILE"; then

echo -e "\n    ######### $country #########" >> "$APACHE_BLOCK_DENY_FILE"

fi

echo "    Deny from $ip" >> "$APACHE_BLOCK_DENY_FILE"

# Apache 2.4용: Require not ip 규칙

if ! grep -q "######### $country #########" "$APACHE_BLOCK_REQUIRE_FILE"; then

echo -e "\n        ######### $country #########" >> "$APACHE_BLOCK_REQUIRE_FILE"

fi

echo "        Require not ip $ip" >> "$APACHE_BLOCK_REQUIRE_FILE"

done

# 6. Apache 2.2용 <Location> 블록 마무리

cat <<EOF >> "$APACHE_BLOCK_DENY_FILE"

Allow from all

</Location>

EOF

# 7. Apache 2.4용 <Location> 블록 마무리

cat <<EOF >> "$APACHE_BLOCK_REQUIRE_FILE"

</RequireAll>

</Location>

EOF

echo "Apache block rules generated successfully:"

echo "  - Apache 2.2 file: $APACHE_BLOCK_DENY_FILE"

echo "  - Apache 2.4 file: $APACHE_BLOCK_REQUIRE_FILE"

###### abuseipdb.com 에서 의심 IP Export 및 apache 버전별 파일 생성 스크립트 ######

# Auto-generated blacklist for Apache 2.2

# Last updated: 2025. 01. 03. (湲 ) 15:27:32 KST

<Location "/">

Order allow,deny

######### AD #########

Deny from 109.111.112.83

######### AE #########

Deny from 152.32.181.210

Deny from 154.85.78.192

Deny from 165.154.217.70

######### ZW #########

Deny from 197.221.232.44

Deny from 197.221.234.19

Allow from all

</Location>

+++++++++++++++++++++++++++++++++++++++++++++++++++++

<Location "/">

<RequireAll>

######### AD #########

Require not ip 109.111.112.83

######### AE #########

Require not ip 152.32.181.210

Require not ip 154.85.78.192

######### ZW #########

Require not ip 197.221.232.44

Require not ip 197.221.234.19

</RequireAll>

</Location>

[코드 수정](Apache%20%E1%84%8B%E1%85%A6%E1%84%89%E1%85%A5%20%E1%84%91%E1%85%A7%E1%86%BC%E1%84%91%E1%85%A1%E1%86%AB%E1%84%83%E1%85%A9%20%E1%84%80%E1%85%B5%E1%84%87%E1%85%A1%E1%86%AB%20IP%20%E1%84%8E%E1%85%A1%E1%84%83%E1%85%A1%E1%86%AB%20238aef0fed1380a794b3e53506c69566/%E1%84%8F%E1%85%A9%E1%84%83%E1%85%B3%20%E1%84%89%E1%85%AE%E1%84%8C%E1%85%A5%E1%86%BC%20238aef0fed1380f88380f10cd75b69d9.md)