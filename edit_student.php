<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$xh = trim($_GET['xh'] ?? $_POST['xh'] ?? '');
if ($xh === '') {
    render_header('编辑学生', 'students');
    echo '<p class="msg error">缺少学号参数。</p>';
    echo '<p class="page-links"><a href="list_students.php">返回学生列表</a></p>';
    render_footer();
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xm = trim($_POST['xm'] ?? '');
    $xb_input = trim($_POST['xb'] ?? '');
    $csrq = trim($_POST['csrq'] ?? '');
    $loan_price = $_POST['loan_price'] ?? 0;

    if ($xm === '') {
        $err = '姓名不能为空。';
    } else {
        $xb = ($xb_input === '女') ? 0 : -1;
        $csrq = $csrq === '' ? null : $csrq;
        $loan_price = $loan_price === '' ? 0 : (float)$loan_price;

        $stmt = $conn->prepare('UPDATE xs SET xm = ?, xb = ?, csrq = ?, loan_price = ? WHERE xh = ?');
        $stmt->bind_param('sisds', $xm, $xb, $csrq, $loan_price, $xh);
        if ($stmt->execute()) {
            redirect_with_msg('list_students.php', '学生 ' . $xh . ' 已更新');
        }
        $err = '更新失败：' . $stmt->error;
        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT xh, xm, xb, csrq, loan_price FROM xs WHERE xh = ?');
$stmt->bind_param('s', $xh);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

render_header('编辑学生', 'students');
if (!$student) {
    echo '<p class="msg error">未找到学号 ' . h($xh) . ' 的学生。</p>';
    echo '<p class="page-links"><a href="list_students.php">返回学生列表</a></p>';
    render_footer();
    exit;
}
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="edit_student.php">
  <input type="hidden" name="xh" value="<?= h($student['xh']) ?>">
  <div class="form-row">
    <label>学号</label>
    <span><?= h($student['xh']) ?> <span class="hint">学号不可修改</span></span>
  </div>
  <div class="form-row">
    <label for="xm">姓名</label>
    <input type="text" id="xm" name="xm" value="<?= h($student['xm']) ?>" required>
  </div>
  <div class="form-row">
    <label for="xb">性别</label>
    <input type="text" id="xb" name="xb" value="<?= $student['xb'] == 0 ? '女' : '男' ?>" placeholder="男 / 女">
  </div>
  <div class="form-row">
    <label for="csrq">出生日期</label>
    <input type="date" id="csrq" name="csrq" value="<?= h($student['csrq']) ?>">
  </div>
  <div class="form-row">
    <label for="loan_price">贷款金额</label>
    <input type="text" id="loan_price" name="loan_price" value="<?= h($student['loan_price']) ?>">
  </div>
  <div class="form-actions">
    <input type="submit" value="保存">
    <a class="btn light" href="list_students.php">取消</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
