<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$err = '';
$teachers = $conn->query('SELECT gh, xm FROM js ORDER BY gh');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kcbh = trim($_POST['kcbh'] ?? '');
    $kcm = trim($_POST['kcm'] ?? '');
    $rkjs = trim($_POST['rkjs'] ?? '');

    if ($kcbh === '' || $kcm === '') {
        $err = '课程编号和课程名不能为空。';
    } else {
        $rkjs = $rkjs === '' ? null : $rkjs;
        $stmt = $conn->prepare('INSERT INTO kc (kcbh, kcm, rkjs) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $kcbh, $kcm, $rkjs);
        if ($stmt->execute()) {
            redirect_with_msg('list_courses.php', '课程 ' . $kcm . ' 添加成功');
        }
        $err = '添加失败：' . $stmt->error;
        $stmt->close();
    }
}

render_header('新增课程', 'courses');
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="add_course.php">
  <div class="form-row">
    <label for="kcbh">课程编号</label>
    <input type="text" id="kcbh" name="kcbh" required>
  </div>
  <div class="form-row">
    <label for="kcm">课程名</label>
    <input type="text" id="kcm" name="kcm" required>
  </div>
  <div class="form-row">
    <label for="rkjs">任课教师</label>
    <select id="rkjs" name="rkjs">
      <option value="">暂不指定</option>
<?php while ($t = $teachers->fetch_assoc()): ?>
      <option value="<?= h($t['gh']) ?>"><?= h($t['xm']) ?>（<?= h($t['gh']) ?>）</option>
<?php endwhile; ?>
    </select>
  </div>
  <div class="form-actions">
    <input type="submit" value="提交">
    <a class="btn light" href="list_courses.php">返回列表</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
