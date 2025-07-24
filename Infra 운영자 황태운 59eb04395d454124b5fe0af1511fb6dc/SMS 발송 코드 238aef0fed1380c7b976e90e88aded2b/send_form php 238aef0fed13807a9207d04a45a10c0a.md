# send_form.php

<?php

session_start();                                 // ★추가
if (!isset($_SESSION['sms_token'])) {
$_SESSION['sms_token'] = bin2hex(random_bytes(16));   // 32-byte 난수
}

$addressBook = [];
if (file_exists('address_book.json')) {
$addressBook = json_decode(file_get_contents('address_book.json'), true);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title> SMS 전송 폼</title>
<style>
body {
font-family: 'Malgun Gothic', sans-serif;
font-size: 16px;
}

```
label {
  display: block;
  margin-top: 12px;
  font-size: 1.3em;
  font-weight: bold;
}

input[type="text"],
input[type="file"],
textarea,
select {
  font-size: 1.3em;
  padding: 10px;
  width: 100%;
  max-width: 600px;
  box-sizing: border-box;
  margin-top: 5px;
}

textarea {
  height: 120px;
}

.address-book {
  max-width: 600px;
  border: 1px solid #ccc;
  padding: 15px;
  margin-bottom: 20px;
}

.addr-item {
  margin-bottom: 6px;
  font-size: 1.2em;
}

input[type="submit"] {
  font-size: 1.2em;
  padding: 10px 25px;
  margin-top: 20px;
  cursor: pointer;
}

```

</style>
<script>
function checkBeforeSend(form) {
const msg = form.msg.value.trim();
const img = form.image.files[0];
const msgLength = msg.length;

```
  let msgType = "SMS";
  if (img) {
    msgType = "MMS";
  } else if (msgLength > 90) {
    msgType = "LMS";
  }

  form.msg_type.value = msgType;
  form.testmode_yn.value = 'N';

  return confirm("전송할 메시지 타입은 [" + msgType + "] 입니다.\\n계속 진행하시겠습니까?");
}

function applyAddressBook() {
  const checkboxes = document.querySelectorAll('.addr-check:checked');
  const receivers = [];
  const destinations = [];

  checkboxes.forEach(cb => {
    const phone = cb.dataset.phone;
    const name = cb.dataset.name;
    receivers.push(phone);
    destinations.push(`${phone}|${name}`);
  });

  document.querySelector('[name="receiver"]').value = receivers.join(',');
  document.querySelector('[name="destination"]').value = destinations.join(',');
}

```

</script>
</head>
<body>

<h2> SMS 전송 폼</h2>

<!-- ✅ 주소록 체크박스 UI -->
<div class="address-book">
<strong> 주소록에서 수신자 선택:</strong><br>
<?php foreach ($addressBook as $entry): ?>
<div class="addr-item">
<label>
<input type="checkbox" class="addr-check"
data-phone="<?= htmlspecialchars($entry['phone']) ?>"
data-name="<?= htmlspecialchars($entry['name']) ?>"
onchange="applyAddressBook()">
<?= htmlspecialchars($entry['name']) ?> (<?= htmlspecialchars($entry['phone']) ?>)
</label>
</div>
<?php endforeach; ?>
</div>

<form action="aligo_send.php" method="post" enctype="multipart/form-data" onsubmit="return checkBeforeSend(this)">

<label>수신번호 (쉼표로 구분):</label>
<input type="text" name="receiver" required>

<label>수신자 치환 정보 (번호|이름):</label>
<input type="text" name="destination">

<label>메시지 내용:</label>
<textarea name="msg" required>%고객명%님. 안녕하세요. 테스트 메시지입니다.</textarea>

<label>발신번호:</label>
<input type="text" name="sender" value="01099271179" required>

<label>제목 (LMS/MMS용):</label>
<input type="text" name="subject" value="테스트 제목">

<input type="hidden" name="msg_type" value="SMS">
<input type="hidden" name="testmode_yn" value="N">

<label>이미지 파일 (MMS용):</label>
<input type="file" name="image" accept="image/*">

<input type="submit" value=" 문자 전송하기">

</form>

</body>
</html>