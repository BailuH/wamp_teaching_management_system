<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$filter_xh = trim($_GET['xh'] ?? '');
$filter_kcbh = trim($_GET['kcbh'] ?? '');
$msg = $_GET['msg'] ?? '';

$students = $conn->query('SELECT xh, xm FROM xs ORDER BY xh');
$courses = $conn->query('SELECT kcbh, kcm FROM kc ORDER BY kcbh');

$sql = 'SELECT x.xh, s.xm, x.kcbh, c.kcm, x.cj
        FROM xk x
        JOIN xs s ON x.xh = s.xh
        JOIN kc c ON x.kcbh = c.kcbh';
$conditions = array();
$params = array();
$types = '';

if ($filter_xh !== '') {
    $conditions[] = 'x.xh = ?';
    $params[] = $filter_xh;
    $types .= 's';
}
if ($filter_kcbh !== '') {
    $conditions[] = 'x.kcbh = ?';
    $params[] = $filter_kcbh;
    $types .= 's';
}
if (count($conditions) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY x.xh, x.kcbh';

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

render_header('选课成绩管理', 'scores');
render_message($msg);
?>
<form class="search-bar" method="get" action="list_scores.php">
  <label for="xh">学生</label>
  <select id="xh" name="xh">
    <option value="">全部</option>
<?php while ($s = $students->fetch_assoc()): ?>
    <option value="<?= h($s['xh']) ?>"<?= $filter_xh === $s['xh'] ? ' selected' : '' ?>><?= h($s['xm']) ?>（<?= h($s['xh']) ?>）</option>
<?php endwhile; ?>
  </select>
  <label for="kcbh">课程</label>
  <select id="kcbh" name="kcbh">
    <option value="">全部</option>
<?php while ($c = $courses->fetch_assoc()): ?>
    <option value="<?= h($c['kcbh']) ?>"<?= $filter_kcbh === $c['kcbh'] ? ' selected' : '' ?>><?= h($c['kcm']) ?>（<?= h($c['kcbh']) ?>）</option>
<?php endwhile; ?>
  </select>
  <button type="submit">筛选</button>
  <a href="list_scores.php">显示全部</a>
  <a class="btn" href="add_score.php">录入成绩</a>
</form>
<table>
  <tr>
    <th>学号</th>
    <th>学生姓名</th>
    <th>课程编号</th>
    <th>课程名</th>
    <th>成绩</th>
    <th>操作</th>
  </tr>
<?php if ($result->num_rows === 0): ?>
  <tr><td colspan="6" class="muted">没有匹配的选课成绩记录。</td></tr>
<?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['xh']) ?></td>
    <td><?= h($row['xm']) ?></td>
    <td><?= h($row['kcbh']) ?></td>
    <td><?= h($row['kcm']) ?></td>
    <td><?= $row['cj'] === null ? '<span class="muted">未录入</span>' : h($row['cj']) ?></td>
    <td class="actions">
      <a href="edit_score.php?xh=<?= urlencode($row['xh']) ?>&amp;kcbh=<?= urlencode($row['kcbh']) ?>">编辑</a>
      <a href="delete_score.php?xh=<?= urlencode($row['xh']) ?>&amp;kcbh=<?= urlencode($row['kcbh']) ?>">删除</a>
    </td>
  </tr>
<?php endwhile; ?>
</table>
<?php
render_footer();
$result->free();
$stmt->close();
$conn->close();
