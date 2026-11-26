<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$xh = trim($_GET['xh'] ?? $_POST['xh'] ?? '');
$kcbh = trim($_GET['kcbh'] ?? $_POST['kcbh'] ?? '');
if ($xh === '' || $kcbh === '') {
    render_header('删除选课成绩', 'scores');
    echo '<p class="msg error">缺少学号或课程编号参数。</p>';
    echo '<p class="page-links"><a href="list_scores.php">返回选课成绩列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM xk WHERE xh = ? AND kcbh = ?');
    $stmt->bind_param('ss', $xh, $kcbh);
    if ($stmt->execute()) {
        redirect_with_msg('list_scores.php', '选课成绩记录已删除');
    }
    $err = '删除失败：' . $stmt->error;
    $stmt->close();
}

$stmt = $conn->prepare('SELECT x.xh, s.xm, x.kcbh, c.kcm, x.cj
                        FROM xk x
                        JOIN xs s ON x.xh = s.xh
                        JOIN kc c ON x.kcbh = c.kcbh
                        WHERE x.xh = ? AND x.kcbh = ?');
$stmt->bind_param('ss', $xh, $kcbh);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();

render_header('删除选课成绩', 'scores');
if (!$record) {
    echo '<p class="msg error">未找到对应的选课成绩记录。</p>';
    echo '<p class="page-links"><a href="list_scores.php">返回选课成绩列表</a></p>';
    render_footer();
    exit;
}
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<div class="card">
  <p>确定要删除以下选课成绩记录吗？</p>
  <p>
    学生：<strong><?= h($record['xm']) ?></strong>（<?= h($record['xh']) ?>）<br>
    课程：<strong><?= h($record['kcm']) ?></strong>（<?= h($record['kcbh']) ?>）<br>
    成绩：<?= $record['cj'] === null ? '未录入' : h($record['cj']) ?>
  </p>
  <form method="post" action="delete_score.php">
    <input type="hidden" name="xh" value="<?= h($record['xh']) ?>">
    <input type="hidden" name="kcbh" value="<?= h($record['kcbh']) ?>">
    <input class="btn danger" type="submit" value="确认删除">
    <a class="btn light" href="list_scores.php">取消</a>
  </form>
</div>
<?php
render_footer();
$conn->close();
