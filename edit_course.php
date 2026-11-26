<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$kcbh = trim($_GET['kcbh'] ?? $_POST['kcbh'] ?? '');
if ($kcbh === '') {
    render_header('编辑课程', 'courses');
    echo '<p class="msg error">缺少课程编号参数。</p>';
    echo '<p class="page-links"><a href="list_courses.php">返回课程列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kcm = trim($_POST['kcm'] ?? '');
    $rkjs = trim($_POST['rkjs'] ?? '');

    if ($kcm === '') {
        $err = '课程名不能为空。';
    } else {
        $rkjs = $rkjs === '' ? null : $rkjs;
        $stmt = $conn->prepare('UPDATE kc SET kcm = ?, rkjs = ? WHERE kcbh = ?');
        $stmt->bind_param('sss', $kcm, $rkjs, $kcbh);
        if ($stmt->execute()) {
            redirect_with_msg('list_courses.php', '课程 ' . $kcbh . ' 已更新');
        }
        $err = '更新失败：' . $stmt->error;
        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT kcbh, kcm, rkjs FROM kc WHERE kcbh = ?');
$stmt->bind_param('s', $kcbh);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

$teachers = $conn->query('SELECT gh, xm FROM js ORDER BY gh');

render_header('编辑课程', 'courses');
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
<form class="form-card" method="post" action="edit_course.php">
  <input type="hidden" name="kcbh" value="<?= h($course['kcbh']) ?>">
  <div class="form-row">
    <label>课程编号</label>
    <span><?= h($course['kcbh']) ?> <span class="hint">编号不可修改</span></span>
  </div>
  <div class="form-row">
    <label for="kcm">课程名</label>
    <input type="text" id="kcm" name="kcm" value="<?= h($course['kcm']) ?>" required>
  </div>
  <div class="form-row">
    <label for="rkjs">任课教师</label>
    <select id="rkjs" name="rkjs">
      <option value="">暂不指定</option>
<?php while ($t = $teachers->fetch_assoc()): ?>
      <option value="<?= h($t['gh']) ?>"<?= $course['rkjs'] === $t['gh'] ? ' selected' : '' ?>><?= h($t['xm']) ?>（<?= h($t['gh']) ?>）</option>
<?php endwhile; ?>
    </select>
  </div>
  <div class="form-actions">
    <input type="submit" value="保存">
    <a class="btn light" href="list_courses.php">取消</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
