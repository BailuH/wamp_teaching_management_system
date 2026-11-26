<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$err = '';
$xh_value = trim($_GET['xh'] ?? '');
$kcbh_value = trim($_GET['kcbh'] ?? '');
$cj_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xh_value = trim($_POST['xh'] ?? '');
    $kcbh_value = trim($_POST['kcbh'] ?? '');
    $cj_value = trim($_POST['cj'] ?? '');

    if ($xh_value === '' || $kcbh_value === '') {
        $err = '请选择学生和课程。';
    } elseif ($cj_value !== '' && !is_numeric($cj_value)) {
        $err = '成绩必须是数字。';
    } else {
        $cj = $cj_value === '' ? null : (float)$cj_value;
        $stmt = $conn->prepare('INSERT INTO xk (xh, kcbh, cj) VALUES (?, ?, ?)');
        $stmt->bind_param('ssd', $xh_value, $kcbh_value, $cj);
        if ($stmt->execute()) {
            redirect_with_msg('list_scores.php', '选课成绩录入成功');
        }
        $err = '录入失败：' . $stmt->error;
        $stmt->close();
    }
}

$students = $conn->query('SELECT xh, xm FROM xs ORDER BY xh');
$courses = $conn->query('SELECT kcbh, kcm FROM kc ORDER BY kcbh');

render_header('录入选课成绩', 'scores');
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="add_score.php">
  <div class="form-row">
    <label for="xh">学生</label>
    <select id="xh" name="xh" required>
      <option value="">请选择学生</option>
<?php while ($s = $students->fetch_assoc()): ?>
      <option value="<?= h($s['xh']) ?>"<?= $xh_value === $s['xh'] ? ' selected' : '' ?>><?= h($s['xm']) ?>（<?= h($s['xh']) ?>）</option>
<?php endwhile; ?>
    </select>
  </div>
  <div class="form-row">
    <label for="kcbh">课程</label>
    <select id="kcbh" name="kcbh" required>
      <option value="">请选择课程</option>
<?php while ($c = $courses->fetch_assoc()): ?>
      <option value="<?= h($c['kcbh']) ?>"<?= $kcbh_value === $c['kcbh'] ? ' selected' : '' ?>><?= h($c['kcm']) ?>（<?= h($c['kcbh']) ?>）</option>
<?php endwhile; ?>
    </select>
  </div>
  <div class="form-row">
    <label for="cj">成绩</label>
    <input type="text" id="cj" name="cj" value="<?= h($cj_value) ?>" placeholder="留空表示尚未录入">
  </div>
  <div class="form-actions">
    <input type="submit" value="提交">
    <a class="btn light" href="list_scores.php">返回列表</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
