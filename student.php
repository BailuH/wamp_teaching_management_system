<?php
session_start();
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

if (empty($_SESSION['xh'])) {
    header('Location: login.html');
    exit;
}
$xh = $_SESSION['xh'];

$stmt = $conn->prepare('SELECT xh, xm, xb, csrq, loan_price, average_grade FROM xs WHERE xh = ?');
$stmt->bind_param('s', $xh);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

render_header('我的信息', '');
if (!$student) {
    echo '<p class="msg error">未找到学生信息。</p>';
    echo '<p class="page-links"><a href="logout.php">重新登录</a></p>';
    render_footer();
    exit;
}

$scores = $conn->prepare('SELECT x.kcbh, c.kcm, x.cj FROM xk x JOIN kc c ON x.kcbh = c.kcbh WHERE x.xh = ? ORDER BY x.kcbh');
$scores->bind_param('s', $xh);
$scores->execute();
$scoreResult = $scores->get_result();
?>
<div class="card">
  <h3>基本信息</h3>
  <table>
    <tr><th>学号</th><td><?= h($student['xh']) ?></td></tr>
    <tr><th>姓名</th><td><?= h($student['xm']) ?></td></tr>
    <tr><th>性别</th><td><?= $student['xb'] == 0 ? '女' : '男' ?></td></tr>
    <tr><th>出生日期</th><td><?= h($student['csrq']) ?></td></tr>
    <tr><th>贷款金额</th><td><?= h($student['loan_price']) ?></td></tr>
    <tr><th>平均成绩</th><td><?= h(number_format((float)$student['average_grade'], 2)) ?></td></tr>
  </table>
</div>

<div class="card">
  <h3>我的选课成绩</h3>
  <table>
    <tr><th>课程编号</th><th>课程名</th><th>成绩</th></tr>
<?php if ($scoreResult->num_rows === 0): ?>
    <tr><td colspan="3" class="muted">暂无选课记录。</td></tr>
<?php endif; ?>
<?php while ($row = $scoreResult->fetch_assoc()): ?>
    <tr>
      <td><?= h($row['kcbh']) ?></td>
      <td><?= h($row['kcm']) ?></td>
      <td><?= $row['cj'] === null ? '尚未录入' : h($row['cj']) ?></td>
    </tr>
<?php endwhile; ?>
  </table>
</div>
<p class="page-links"><a href="logout.php">退出登录</a></p>
<?php
render_footer();
$scoreResult->free();
$scores->close();
$conn->close();
