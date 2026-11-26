<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$kcbh = trim($_GET['kcbh'] ?? $_POST['kcbh'] ?? '');
if ($kcbh === '') {
    render_header('删除课程', 'courses');
    echo '<p class="msg error">缺少课程编号参数。</p>';
    echo '<p class="page-links"><a href="list_courses.php">返回课程列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM kc WHERE kcbh = ?');
    $stmt->bind_param('s', $kcbh);
    if ($stmt->execute()) {
        redirect_with_msg('list_courses.php', '课程 ' . $kcbh . ' 已删除');
    }
    $err = '删除失败：' . $stmt->error;
    $stmt->close();
}

$stmt = $conn->prepare('SELECT kcbh, kcm FROM kc WHERE kcbh = ?');
$stmt->bind_param('s', $kcbh);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

render_header('删除课程', 'courses');
if (!$course) {
    echo '<p class="msg error">未找到编号为 ' . h($kcbh) . ' 的课程。</p>';
    echo '<p class="page-links"><a href="list_courses.php">返回课程列表</a></p>';
    render_footer();
    exit;
}
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<div class="card">
  <p>确定要删除课程 <strong><?= h($course['kcm']) ?></strong>（<?= h($course['kcbh']) ?>）吗？</p>
  <p class="muted">删除课程后，该课程下的所有选课成绩记录也会被一并删除。</p>
  <form method="post" action="delete_course.php">
    <input type="hidden" name="kcbh" value="<?= h($course['kcbh']) ?>">
    <input class="btn danger" type="submit" value="确认删除">
    <a class="btn light" href="list_courses.php">取消</a>
  </form>
</div>
<?php
render_footer();
$conn->close();
