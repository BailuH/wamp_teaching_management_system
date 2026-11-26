<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin123') {
        header('Location: hwq.html');
        exit;
    }
    $err = '用户名或密码错误。';
}

render_header('超级管理员登录', '');
if ($err !== '') {
    echo '<p class="msg error">' . h($err) . '</p>';
}
?>
<form class="form-card" method="post" action="admin_login.php">
  <div class="form-row">
    <label for="username">用户名</label>
    <input type="text" id="username" name="username" required>
  </div>
  <div class="form-row">
    <label for="password">密码</label>
    <input type="password" id="password" name="password" required>
  </div>
  <div class="form-actions">
    <input type="submit" value="登录">
    <a class="btn light" href="login.html">返回</a>
  </div>
</form>
<p class="page-links"><span class="hint">演示账号：admin / admin123</span></p>
<?php
render_footer();
$conn->close();
