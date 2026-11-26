<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

/* 这里是业务逻辑，删除需要确认，所以要先判断此刻页面状态
如果是GET，说明尚未确认，不执行DELETE语句，先查询是否有，没有则报错，有则提示
如果是POST，说明用户是已经点过红按钮第二次到这个页面了，此时可以执行删除语句，再重定向到list */


$xh = trim($_GET['xh'] ?? $_POST['xh'] ?? '');
if ($xh === '') {
    render_header('删除学生', 'students');
    echo '<p class="msg error">缺少学号参数。</p>';
    echo '<p class="page-links"><a href="list_students.php">返回学生列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM xs WHERE xh = ?');
    $stmt->bind_param('s', $xh);
    if ($stmt->execute()) {
        redirect_with_msg('list_students.php', '学生 ' . $xh . ' 已删除');
    }
    $err = '删除失败：' . $stmt->error;
    $stmt->close();
}

$stmt = $conn->prepare('SELECT xh, xm FROM xs WHERE xh = ?');
$stmt->bind_param('s', $xh);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

render_header('删除学生', 'students');
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
<div class="card">
  <p>确定要删除学生 <strong><?= h($student['xm']) ?></strong>（<?= h($student['xh']) ?>）吗？</p>
  <p class="muted">删除学生后，其所有选课和成绩记录也会被一并删除。</p>
  <form method="post" action="delete_student.php">
    <input type="hidden" name="xh" value="<?= h($student['xh']) ?>">
    <input class="btn danger" type="submit" value="确认删除">
    <a class="btn light" href="list_students.php">取消</a>
  </form>
</div>
<?php
render_footer();
$conn->close();
