# navbar.php

<?php
$current = basename($_SERVER['PHP_SELF']);

// 함수 중복 선언 방지
if (!function_exists('isActive')) {
function isActive($page) {
global $current;
return $current === $page ? 'active' : '';
}
}

// 조건 메뉴 표시 여부 기본값
if (!isset($showSender)) {
$showSender = false;
}

// 메뉴 항목 구성
$menus = [
['label' => '문자보내기', 'href' => 'send_form.php'],
['label' => '잔여건수', 'href' => 'aligo_remain.php'],
['label' => '전송결과', 'href' => 'aligo_list.php'],
['label' => '주소록', 'href' => 'address_book_edit.php'],
];

if ($showSender) {
$menus = array_merge($menus, [
['label' => '발신번호', 'href' => 'sender_list.php'],
['label' => '문자API', 'href' => 'aligo_send.php'],
['label' => '카카오톡', 'href' => 'kakao_list.php'],
]);
}
?>

<style>
.navbar-container {
width: 100%;
display: flex;
justify-content: center;
border-bottom: 2px solid #00b3b3;
background-color: #fff;
margin-bottom: 20px;
}

.navbar {
font-family: 'Malgun Gothic', sans-serif;
font-size: 14px;
padding: 10px 0;
}

.navbar a {
text-decoration: none;
color: #333;
margin: 0 10px;
padding: 6px 4px;
}

.navbar a:hover {
color: #00b3b3;
font-weight: bold;
}

.navbar a.active {
color: #00b3b3;
font-weight: bold;
border-bottom: 2px solid #00b3b3;
}
</style>

<div class="navbar-container">
<div class="navbar">
<?php foreach ($menus as $i => $menu): ?>
<?php if ($i > 0): ?> | <?php endif; ?>
<a href="<?= $menu['href'] ?>" class="<?= isActive($menu['href']) ?>">
<?= $menu['label'] ?>
</a>
<?php endforeach; ?>
</div>
</div>