<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$xh         = trim($_POST['xh'] ?? '');
$xm         = trim($_POST['xm'] ?? '');
$xb_input   = trim($_POST['xb'] ?? '');
$csrq       = trim($_POST['csrq'] ?? '');
$loan_price = $_POST['loan_price'] ?? 0;

if ($xh === '' || $xm === '') {
    render_header('添加学生', 'students');
    echo '<p class="msg error">学号和姓名不能为空。</p>';
    echo '<p class="page-links"><a href="hwq.html">返回录入页</a></p>';
    render_footer();
    exit;
}

$xb = ($xb_input === '女') ? 0 : -1;
$csrq = $csrq === '' ? null : $csrq;
$loan_price = $loan_price === '' ? 0 : (float)$loan_price;

$stmt = $conn->prepare('INSERT INTO xs (xh, xm, xb, csrq, loan_price) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('ssisd', $xh, $xm, $xb, $csrq, $loan_price);

render_header('添加学生', 'students');
if ($stmt->execute()) {
    echo '<p class="msg success">学生 ' . h($xm) . '（' . h($xh) . '）添加成功。</p>';
} else {
    echo '<p class="msg error">添加失败：' . h($stmt->error) . '</p>';
}
echo '<p class="page-links"><a href="hwq.html">继续录入</a><a href="list_students.php">查看学生列表</a></p>';
render_footer();

$stmt->close();
$conn->close();
