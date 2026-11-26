<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$old_xh = trim($_POST['old_xh'] ?? $_GET['xh'] ?? '');
$old_kcbh = trim($_POST['old_kcbh'] ?? $_GET['kcbh'] ?? '');
if ($old_xh === '' || $old_kcbh === '') {
    render_header('编辑成绩', 'scores');
    echo '<p class="msg error">缺少学号或课程编号参数。</p>';
    echo '<p class="page-links"><a href="list_scores.php">返回选课成绩列表</a></p>';
    render_footer();
    exit;
}

$err = '';
$xh_value = $old_xh;
$kcbh_value = $old_kcbh;
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
        $stmt = $conn->prepare('UPDATE xk SET xh = ?, kcbh = ?, cj = ? WHERE xh = ? AND kcbh = ?');
        $stmt->bind_param('ssdss', $xh_value, $kcbh_value, $cj, $old_xh, $old_kcbh);
        if ($stmt->execute()) {
            redirect_with_msg('list_scores.php', '选课成绩已更新');
        }
        $err = '更新失败：' . $stmt->error;
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare('SELECT cj FROM xk WHERE xh = ? AND kcbh = ?');
    $stmt->bind_param('ss', $old_xh, $old_kcbh);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if (!$row) {
        render_header('编辑成绩', 'scores');
        echo '<p class="msg error">未找到对应的选课成绩记录。</p>';
        echo '<p class="page-links"><a href="list_scores.php">返回选课成绩列表</a></p>';
        render_footer();
        exit;
    }
    $cj_value = $row['cj'] === null ? '' : (string)$row['cj'];
}

$students = $conn->query('SELECT xh, xm FROM xs ORDER BY xh');
$courses = $conn->query('SELECT kcbh, kcm FROM kc ORDER BY kcbh');

render_header('编辑选课成绩', 'scores');
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="edit_score.php">
  <input type="hidden" name="old_xh" value="<?= h($old_xh) ?>">
  <input type="hidden" name="old_kcbh" value="<?= h($old_kcbh) ?>">
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
    <input type="submit" value="保存">
    <a class="btn light" href="list_scores.php">取消</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
