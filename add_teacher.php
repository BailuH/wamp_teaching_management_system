<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gh = trim($_POST['gh'] ?? '');
    $xm = trim($_POST['xm'] ?? '');
    $xb = trim($_POST['xb'] ?? '');
    $zc = trim($_POST['zc'] ?? '');
    $yx = trim($_POST['yx'] ?? '');

    if ($gh === '' || $xm === '') {
        $err = '工号和姓名不能为空。';
    } elseif ($xb !== '男' && $xb !== '女') {
        $err = '性别必须为“男”或“女”。';
    } else {
        $stmt = $conn->prepare('INSERT INTO js (gh, xm, xb, zc, yx) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $gh, $xm, $xb, $zc, $yx);
        if ($stmt->execute()) {
            redirect_with_msg('list_teachers.php', '教师 ' . $xm . ' 添加成功');
        }
        $err = '添加失败：' . $stmt->error;
        $stmt->close();
    }
}

render_header('新增教师', 'teachers');
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="add_teacher.php">
  <div class="form-row">
    <label for="gh">工号</label>
    <input type="text" id="gh" name="gh" required>
  </div>
  <div class="form-row">
    <label for="xm">姓名</label>
    <input type="text" id="xm" name="xm" required>
  </div>
  <div class="form-row">
    <label for="xb">性别</label>
    <input type="text" id="xb" name="xb" placeholder="男 / 女" required>
  </div>
  <div class="form-row">
    <label for="zc">职称</label>
    <input type="text" id="zc" name="zc" placeholder="教授 / 副教授 / 讲师">
  </div>
  <div class="form-row">
    <label for="yx">院系</label>
    <input type="text" id="yx" name="yx">
  </div>
  <div class="form-actions">
    <input type="submit" value="提交">
    <a class="btn light" href="list_teachers.php">返回列表</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
