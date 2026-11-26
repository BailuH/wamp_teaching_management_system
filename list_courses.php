<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$q = trim($_GET['q'] ?? '');
$msg = $_GET['msg'] ?? '';

$sql = 'SELECT k.kcbh, k.kcm, k.rkjs, j.xm AS teacher_name,
               COUNT(x.xh) AS student_count, ROUND(AVG(x.cj), 2) AS avg_score
        FROM kc k
        LEFT JOIN js j ON k.rkjs = j.gh
        LEFT JOIN xk x ON k.kcbh = x.kcbh';
if ($q !== '') {
    $sql .= ' WHERE k.kcbh LIKE ? OR k.kcm LIKE ? OR j.xm LIKE ?';
}
$sql .= ' GROUP BY k.kcbh, k.kcm, k.rkjs, j.xm ORDER BY k.kcbh';

$stmt = $conn->prepare($sql);
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt->bind_param('sss', $like, $like, $like);
}
$stmt->execute();
$result = $stmt->get_result();

render_header('课程管理', 'courses');
render_message($msg);
?>
<form class="search-bar" method="get" action="list_courses.php">
  <label for="q">搜索</label>
  <input type="text" id="q" name="q" value="<?= h($q) ?>" placeholder="课程编号、课程名或任课教师">
  <button type="submit">查询</button>
  <a href="list_courses.php">显示全部</a>
  <a class="btn" href="add_course.php">新增课程</a>
</form>
<table>
  <tr>
    <th>课程编号</th>
    <th>课程名</th>
    <th>任课教师</th>
    <th>选课人数</th>
    <th>平均分</th>
    <th>操作</th>
  </tr>
<?php if ($result->num_rows === 0): ?>
  <tr><td colspan="6" class="muted">没有匹配的课程。</td></tr>
<?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['kcbh']) ?></td>
    <td><?= h($row['kcm']) ?></td>
    <td><?= h($row['teacher_name'] ?: '—') ?><span class="muted"><?= $row['rkjs'] ? '（' . h($row['rkjs']) . '）' : '' ?></span></td>
    <td><?= h($row['student_count']) ?></td>
    <td><?= $row['avg_score'] === null ? '—' : h($row['avg_score']) ?></td>
    <td class="actions">
      <a href="edit_course.php?kcbh=<?= urlencode($row['kcbh']) ?>">编辑</a>
      <a href="delete_course.php?kcbh=<?= urlencode($row['kcbh']) ?>">删除</a>
    </td>
  </tr>
<?php endwhile; ?>
</table>
<?php
render_footer();
$result->free();
$stmt->close();
$conn->close();
