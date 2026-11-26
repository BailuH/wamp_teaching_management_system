<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$msg = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = 'UPDATE xs
            LEFT JOIN (SELECT xh, ROUND(AVG(cj), 2) AS avg_score FROM xk GROUP BY xh) t
              ON xs.xh = t.xh
            SET xs.average_grade = IFNULL(t.avg_score, 0)';
    if ($conn->query($sql)) {
        redirect_with_msg('stats.php', '已根据选课成绩重新计算所有学生的平均成绩');
    }
    $msg = '重算失败：' . $conn->error;
}

$studentStats = $conn->query('SELECT s.xh, s.xm, s.average_grade,
                                     COUNT(x.cj) AS graded_count,
                                     ROUND(AVG(x.cj), 2) AS calc_avg,
                                     MAX(x.cj) AS max_score,
                                     MIN(x.cj) AS min_score
                              FROM xs s
                              LEFT JOIN xk x ON s.xh = x.xh
                              GROUP BY s.xh, s.xm, s.average_grade
                              ORDER BY s.xh');

$courseStats = $conn->query('SELECT c.kcbh, c.kcm, j.xm AS teacher_name,
                                    COUNT(x.xh) AS student_count,
                                    COUNT(x.cj) AS graded_count,
                                    ROUND(AVG(x.cj), 2) AS avg_score,
                                    MAX(x.cj) AS max_score,
                                    MIN(x.cj) AS min_score
                             FROM kc c
                             LEFT JOIN js j ON c.rkjs = j.gh
                             LEFT JOIN xk x ON c.kcbh = x.kcbh
                             GROUP BY c.kcbh, c.kcm, j.xm
                             ORDER BY c.kcbh');

render_header('成绩统计', 'stats');
if ($msg !== '') {
    $class = strpos($msg, '失败') === 0 ? 'error' : 'success';
    echo '<p class="msg ' . $class . '">' . h($msg) . '</p>';
}
?>
<div class="card">
  <h3>平均成绩维护</h3>
  <p class="muted">将依据 xk 表中的非空成绩，重新计算并写回 xs.average_grade。</p>
  <form method="post" action="stats.php">
    <input type="submit" value="重算所有学生平均成绩">
  </form>
</div>

<div class="card">
  <h3>学生成绩统计</h3>
  <table>
    <tr>
      <th>学号</th>
      <th>姓名</th>
      <th>已录成绩数</th>
      <th>计算平均分</th>
      <th>最高分</th>
      <th>最低分</th>
      <th>表中平均分</th>
    </tr>
<?php if ($studentStats->num_rows === 0): ?>
    <tr><td colspan="7" class="muted">暂无学生数据。</td></tr>
<?php endif; ?>
<?php while ($row = $studentStats->fetch_assoc()): ?>
    <tr>
      <td><?= h($row['xh']) ?></td>
      <td><?= h($row['xm']) ?></td>
      <td><?= h($row['graded_count']) ?></td>
      <td><?= $row['calc_avg'] === null ? '—' : h($row['calc_avg']) ?></td>
      <td><?= $row['max_score'] === null ? '—' : h($row['max_score']) ?></td>
      <td><?= $row['min_score'] === null ? '—' : h($row['min_score']) ?></td>
      <td><?= h(number_format((float)$row['average_grade'], 2)) ?></td>
    </tr>
<?php endwhile; ?>
  </table>
</div>

<div class="card">
  <h3>课程成绩统计</h3>
  <table>
    <tr>
      <th>课程编号</th>
      <th>课程名</th>
      <th>任课教师</th>
      <th>选课人数</th>
      <th>已录成绩数</th>
      <th>平均分</th>
      <th>最高分</th>
      <th>最低分</th>
    </tr>
<?php if ($courseStats->num_rows === 0): ?>
    <tr><td colspan="8" class="muted">暂无课程数据。</td></tr>
<?php endif; ?>
<?php while ($row = $courseStats->fetch_assoc()): ?>
    <tr>
      <td><?= h($row['kcbh']) ?></td>
      <td><?= h($row['kcm']) ?></td>
      <td><?= h($row['teacher_name'] ?: '—') ?></td>
      <td><?= h($row['student_count']) ?></td>
      <td><?= h($row['graded_count']) ?></td>
      <td><?= $row['avg_score'] === null ? '—' : h($row['avg_score']) ?></td>
      <td><?= $row['max_score'] === null ? '—' : h($row['max_score']) ?></td>
      <td><?= $row['min_score'] === null ? '—' : h($row['min_score']) ?></td>
    </tr>
<?php endwhile; ?>
  </table>
</div>
<?php
render_footer();
$studentStats->free();
$courseStats->free();
$conn->close();
