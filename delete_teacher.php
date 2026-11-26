<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$gh = trim($_GET['gh'] ?? $_POST['gh'] ?? '');
if ($gh === '') {
    render_header('删除教师', 'teachers');
    echo '<p class="msg error">缺少工号参数。</p>';
    echo '<p class="page-links"><a href="list_teachers.php">返回教师列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM js WHERE gh = ?');
    $stmt->bind_param('s', $gh);
    if ($stmt->execute()) {
        redirect_with_msg('list_teachers.php', '教师 ' . $gh . ' 已删除');
    }
    $err = '删除失败：' . $stmt->error;
    $stmt->close();
}

$stmt = $conn->prepare('SELECT gh, xm FROM js WHERE gh = ?');
$stmt->bind_param('s', $gh);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

render_header('删除教师', 'teachers');
if (!$teacher) {
    echo '<p class="msg error">未找到工号 ' . h($gh) . ' 的教师。</p>';
    echo '<p class="page-links"><a href="list_teachers.php">返回教师列表</a></p>';
    render_footer();
    exit;
}
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<div class="card">
  <p>确定要删除教师 <strong><?= h($teacher['xm']) ?></strong>（<?= h($teacher['gh']) ?>）吗？</p>
  <p class="muted">删除教师会级联删除其任课课程，以及这些课程下的选课成绩记录。</p>
  <form method="post" action="delete_teacher.php">
    <input type="hidden" name="gh" value="<?= h($teacher['gh']) ?>">
    <input class="btn danger" type="submit" value="确认删除">
    <a class="btn light" href="list_teachers.php">取消</a>
  </form>
</div>
<?php
render_footer();
$conn->close();
