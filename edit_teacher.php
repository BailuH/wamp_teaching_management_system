<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$gh = trim($_GET['gh'] ?? $_POST['gh'] ?? '');
if ($gh === '') {
    render_header('编辑教师', 'teachers');
    echo '<p class="msg error">缺少工号参数。</p>';
    echo '<p class="page-links"><a href="list_teachers.php">返回教师列表</a></p>';
    render_footer();
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xm = trim($_POST['xm'] ?? '');
    $xb = trim($_POST['xb'] ?? '');
    $zc = trim($_POST['zc'] ?? '');
    $yx = trim($_POST['yx'] ?? '');

    if ($xm === '') {
        $err = '姓名不能为空。';
    } elseif ($xb !== '男' && $xb !== '女') {
        $err = '性别必须为“男”或“女”。';
    } else {
        $stmt = $conn->prepare('UPDATE js SET xm = ?, xb = ?, zc = ?, yx = ? WHERE gh = ?');
        $stmt->bind_param('sssss', $xm, $xb, $zc, $yx, $gh);
        if ($stmt->execute()) {
            redirect_with_msg('list_teachers.php', '教师 ' . $gh . ' 已更新');
        }
        $err = '更新失败：' . $stmt->error;
        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT gh, xm, xb, zc, yx FROM js WHERE gh = ?');
$stmt->bind_param('s', $gh);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

render_header('编辑教师', 'teachers');
if (!$teacher) {
    echo '<p class="msg error">未找到工号 ' . h($gh) . ' 的教师。</p>';
    echo '<p class="page-links"><a href="list_teachers.php">返回教师列表</a></p>';
    render_footer();
    exit;
}
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="edit_teacher.php">
  <input type="hidden" name="gh" value="<?= h($teacher['gh']) ?>">
  <div class="form-row">
    <label>工号</label>
    <span><?= h($teacher['gh']) ?> <span class="hint">工号不可修改</span></span>
  </div>
  <div class="form-row">
    <label for="xm">姓名</label>
    <input type="text" id="xm" name="xm" value="<?= h($teacher['xm']) ?>" required>
  </div>
  <div class="form-row">
    <label for="xb">性别</label>
    <input type="text" id="xb" name="xb" value="<?= h($teacher['xb']) ?>" required>
  </div>
  <div class="form-row">
    <label for="zc">职称</label>
    <input type="text" id="zc" name="zc" value="<?= h($teacher['zc']) ?>">
  </div>
  <div class="form-row">
    <label for="yx">院系</label>
    <input type="text" id="yx" name="yx" value="<?= h($teacher['yx']) ?>">
  </div>
  <div class="form-actions">
    <input type="submit" value="保存">
    <a class="btn light" href="list_teachers.php">取消</a>
  </div>
</form>
<?php
render_footer();
$conn->close();
