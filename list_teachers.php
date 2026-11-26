<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$q = trim($_GET['q'] ?? '');
$msg = $_GET['msg'] ?? '';

$sql = 'SELECT j.gh, j.xm, j.xb, j.zc, j.yx, COUNT(k.kcbh) AS course_count
        FROM js j LEFT JOIN kc k ON j.gh = k.rkjs';
if ($q !== '') {
    $sql .= ' WHERE j.gh LIKE ? OR j.xm LIKE ? OR j.yx LIKE ?';
}
$sql .= ' GROUP BY j.gh, j.xm, j.xb, j.zc, j.yx ORDER BY j.gh';

$stmt = $conn->prepare($sql);
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt->bind_param('sss', $like, $like, $like);
}
$stmt->execute();
$result = $stmt->get_result();

render_header('教师管理', 'teachers');
render_message($msg);
?>
<form class="search-bar" method="get" action="list_teachers.php">
  <label for="q">搜索</label>
  <input type="text" id="q" name="q" value="<?= h($q) ?>" placeholder="工号、姓名或院系">
  <button type="submit">查询</button>
  <a href="list_teachers.php">显示全部</a>
  <a class="btn" href="add_teacher.php">新增教师</a>
</form>
<table>
  <tr>
    <th>工号</th>
    <th>姓名</th>
    <th>性别</th>
    <th>职称</th>
    <th>院系</th>
    <th>任课数</th>
    <th>操作</th>
  </tr>
<?php if ($result->num_rows === 0): ?>
  <tr><td colspan="7" class="muted">没有匹配的教师。</td></tr>
<?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['gh']) ?></td>
    <td><?= h($row['xm']) ?></td>
    <td><?= h($row['xb']) ?></td>
    <td><?= h($row['zc']) ?></td>
    <td><?= h($row['yx']) ?></td>
    <td><?= h($row['course_count']) ?></td>
    <td class="actions">
      <a href="edit_teacher.php?gh=<?= urlencode($row['gh']) ?>">编辑</a>
      <a href="delete_teacher.php?gh=<?= urlencode($row['gh']) ?>">删除</a>
    </td>
  </tr>
<?php endwhile; ?>
</table>
<?php
render_footer();
$result->free();
$stmt->close();
$conn->close();
