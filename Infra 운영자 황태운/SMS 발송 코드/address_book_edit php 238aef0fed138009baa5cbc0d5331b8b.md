# address_book_edit.php

<?php
// address_book_edit.php
$addressFile = 'address_book.json';
$addressBook = file_exists($addressFile)
? json_decode(file_get_contents($addressFile), true)
: [];

$success = null;

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$action = $_POST['action'] ?? '';
$index = isset($_POST['index']) ? (int)$_POST['index'] : null;
$name = trim($_POST['name'] ?? '');
$rawPhone = trim($_POST['phone'] ?? '');
$phone = preg_replace('/[^0-9]/', '', $rawPhone);

```
if (preg_match('/[가-힣a-zA-Z]/u', $rawPhone)) {
    echo "<script>alert('전화번호만 입력 바랍니다.'); history.back();</script>";
    exit;
}

if ($action === 'add' && $name && $phone) {
    $addressBook[] = ['name' => $name, 'phone' => $phone];
    $success = "✅ 주소록에 추가되었습니다.";
} elseif ($action === 'update' && isset($addressBook[$index])) {
    $addressBook[$index] = ['name' => $name, 'phone' => $phone];
    $success = "✅ 수정되었습니다.";
} elseif ($action === 'delete' && isset($addressBook[$index])) {
    array_splice($addressBook, $index, 1);
    $success = "✅ 삭제되었습니다.";
}

file_put_contents($addressFile, json_encode($addressBook, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
header("Location: " . $_SERVER['PHP_SELF']);
exit;

```

}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>주소록 관리</title>
<style>
body { font-family: sans-serif; max-width: 700px; margin: 40px auto; }
input[type="text"] { width: 100%; padding: 8px; font-size: 1em; margin-bottom: 5px; }
input[type="submit"] { padding: 8px 16px; font-size: 1em; }
table { width: 100%; border-collapse: collapse; margin-top: 30px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
h2 { margin-top: 40px; }
form.inline { display: inline; }
</style>
</head>
<body>

<h1>📒 주소록 추가</h1>
<form method="post">
<input type="hidden" name="action" value="add">
<label>이름:</label><br>
<input type="text" name="name" required><br>
<label>전화번호:</label><br>
<input type="text" name="phone" required><br>
<input type="submit" value="📥 추가하기">
</form>

<h2>📋 현재 주소록</h2>
<table>
<tr><th>#</th><th>이름</th><th>전화번호</th><th>수정/삭제</th></tr>
<?php foreach ($addressBook as $i => $entry): ?>
<tr>
<form method="post">
<td><?= $i + 1 ?></td>
<td>
<input type="text" name="name" value="<?= htmlspecialchars($entry['name']) ?>" required>
</td>
<td>
<input type="text" name="phone" value="<?= htmlspecialchars($entry['phone']) ?>" required>
</td>
<td>
<input type="hidden" name="index" value="<?= $i ?>">
<button type="submit" name="action" value="update">수정</button>
<button type="submit" name="action" value="delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>