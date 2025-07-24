# 코드 수정

사유 : apache 2.4 이상에서 기존 코드 에러 발생
block_ips_require.conf 에 Require all granted 를 추가하게 수정

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

Require all granted

EOF

# 5. 국가별로 IP 정리 및 규칙 생성

echo "Generating Apache block rules grouped by country..."

grep -oP '"countryCode":"\K[^"]+|(?<="ipAddress":")[^"]+' "$JSON_FILE" | paste - - | sort | while IFS=$'\t' read -r country ip; do

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