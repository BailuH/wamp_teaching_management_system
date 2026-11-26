<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$q = trim($_GET['q'] ?? '');
$msg = $_GET['msg'] ?? '';

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare('SELECT xh, xm, xb, csrq, loan_price, average_grade FROM xs WHERE xh LIKE ? OR xm LIKE ? ORDER BY xh');
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query('SELECT xh, xm, xb, csrq, loan_price, average_grade FROM xs ORDER BY xh');
}

render_header('学生管理', 'students');
render_message($msg);
?>
<form class="search-bar" method="get" action="list_students.php">
  <label for="q">搜索</label>
  <input type="text" id="q" name="q" value="<?= h($q) ?>" placeholder="学号或姓名">
  <button type="submit">查询</button>
  <a href="list_students.php">显示全部</a>
  <a class="btn" href="hwq.html">新增学生</a>
</form>
<table>
  <tr>
    <th>学号</th>
    <th>姓名</th>
    <th>性别</th>
    <th>出生日期</th>
    <th>贷款金额</th>
    <th>平均成绩</th>
    <th>操作</th>
  </tr>
<?php if ($result->num_rows === 0): ?>
  <tr><td colspan="7" class="muted">没有匹配的学生。</td></tr>
<?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['xh']) ?></td>
    <td><?= h($row['xm']) ?></td>
    <td><?= $row['xb'] == 0 ? '女' : '男' ?></td>
    <td><?= h($row['csrq']) ?></td>
    <td><?= h($row['loan_price']) ?></td>
    <td><?= h(number_format((float)$row['average_grade'], 2)) ?></td>
    <td class="actions">
      <a href="edit_student.php?xh=<?= urlencode($row['xh']) ?>">编辑</a>
      <a href="add_score.php?xh=<?= urlencode($row['xh']) ?>">选课</a>
      <a href="delete_student.php?xh=<?= urlencode($row['xh']) ?>">删除</a>
    </td>
  </tr>
<?php endwhile; ?>
</table>
<?php
render_footer();

$result->free();
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
