# PHP 语法速通（以本项目教务管理系统为例）

本项目的场景是：在 WAMP（Windows + Apache + MySQL + PHP）环境下，用 PHP 写一个教务管理系统，对学生 `xs`、教师 `js`、课程 `kc`、选课 `xk` 四张表做增删改查和统计。本文按实际代码出现的顺序讲解 PHP 语法。

---

## 1. PHP 标记：`<?php ?>` 与 `<?= ?>`

PHP 代码必须包在 `<?php ... ?>` 之间。当只想“输出一个值”时，可用简写 `<?= 表达式 ?>`，它等价于 `<?php echo 表达式; ?>`。

```php
<?php
require __DIR__ . '/includes/db.php';   // 纯 PHP 段
?>

<!-- HTML 中插入 PHP 变量 -->
<input type="text" name="xh" value="<?= h($student['xh']) ?>">
```

> 例子来自 `edit_student.php`：表单的 value 里直接用 `<?= h($student['xh']) ?>` 把学号填进输入框。

---

## 2. 变量与数据类型

变量以 `$` 开头，无需声明类型，PHP 自动推断。常见类型：字符串、整数、浮点数、布尔、null、数组。

```php
$xh         = trim($_POST['xh'] ?? '');   // 字符串
$loan_price = $_POST['loan_price'] ?? 0;  // 可能是字符串或数字
$loan_price = (float)$loan_price;         // 强制转为浮点
$csrq       = $csrq === '' ? null : $csrq; // null 表示“没填”
```

- `(float)$x`：类型转换，把字符串转成浮点。
- `null`：常用于往数据库写 NULL 值。

> 例子来自 `add_student.php`。

---

## 3. 运算符

### 3.1 空合并运算符 `??`

`$a ?? $b` 表示“如果 `$a` 存在且不为 null 就用 `$a`，否则用 `$b`”。本项目里大量用于安全地读取表单参数：

```php
$xh = trim($_POST['xh'] ?? '');          // 没传就用空串
$msg = $_GET['msg'] ?? '';               // URL 没有 msg 就当空
$xh = trim($_GET['xh'] ?? $_POST['xh'] ?? ''); // 链式：GET 没有就取 POST
```

### 3.2 三元运算符 `条件 ? 值1 : 值2`

```php
$xb = ($xb_input === '女') ? 0 : -1;
$csrq = $csrq === '' ? null : $csrq;
$sep = strpos($url, '?') === false ? '?' : '&';
```

### 3.3 比较与逻辑

`==` 值相等，`===` 全等（类型也相同），`!==` 不全等。`&&`、`||`、`!` 是逻辑与/或/非。

```php
if ($xh === '' || $xm === '') { ... }    // 学号或姓名为空
if ($cj_value !== '' && !is_numeric($cj_value)) { ... }
```

---

## 4. 字符串

### 4.1 单引号 vs 双引号

单引号：原样输出，只转义 `\'` 和 `\\`。双引号：可解析变量和转义符 `\n \t`。

```php
$stmt = $conn->prepare('INSERT INTO xs (xh, xm, xb, csrq, loan_price) VALUES (?, ?, ?, ?, ?)');
echo '添加失败：' . h($stmt->error);    // . 是字符串连接符
```

### 4.2 常用字符串函数

```php
trim($xh)                    // 去首尾空白
htmlspecialchars($v, ENT_QUOTES, 'UTF-8')  // 转义 HTML，防 XSS
urlencode($msg)              // URL 编码，用于拼 query string
strpos($url, '?')            // 查子串位置，找不到返回 false
number_format($avg, 2)       // 保留两位小数
```

> `h()` 是 `includes/db.php` 里自定义的包装函数，把 `htmlspecialchars` 缩写一下。

---

## 5. 数组

PHP 数组是有序映射，既能当索引数组，也能当关联数组（键是字符串）。

### 5.1 关联数组

```php
$items = array(
    'home'     => array('hwq.html', '首页 / 录入'),
    'students' => array('list_students.php', '学生管理'),
);
```

### 5.2 取元素

```php
$row = $result->fetch_assoc();   // 从结果集取一行，得到关联数组
echo $row['xh'];                 // 用列名取值
echo $row['xm'];
```

> `list_students.php` 里 `$row['xh']`、`$row['xm']`、`$row['loan_price']` 都来自数据库一行。

---

## 6. 控制结构

### 6.1 if / elseif / else

```php
if ($xh === '' || $xm === '') {
    render_header('添加学生', 'students');
    echo '<p class="msg error">学号和姓名不能为空。</p>';
    exit;
} elseif ($xm === '') {
    $err = '姓名不能为空。';
} else {
    // 正常流程
}
```

### 6.2 while 循环：遍历结果集

```php
while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['xh']) ?></td>
    <td><?= h($row['xm']) ?></td>
  </tr>
<?php endwhile; ?>
```

`fetch_assoc()` 每次取一行，取完返回 `false`，循环自然结束。`while (...):` ... `endwhile;` 是模板里更清爽的写法，等价于 `{ }`。

### 6.3 foreach：遍历数组

```php
foreach ($items as $key => $item) {
    $class = $key === $active ? ' class="active"' : '';
    echo '<a href="' . $item[0] . '"' . $class . '>' . h($item[1]) . '</a>';
}
```

> 例子来自 `includes/layout.php`：根据当前页 `$active` 给导航项加高亮 class。

---

## 7. 函数

用 `function` 定义，无需声明返回类型。本项目把公共逻辑都放在 `includes/` 里。

```php
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_msg($url, $msg) {
    $sep = strpos($url, '?') === false ? '?' : '&';
    header('Location: ' . $url . $sep . 'msg=' . urlencode($msg));
    exit;
}

function render_header($title, $active = '') {  // $active 是默认参数
    // ... echo 一堆 HTML
}
```

- `header('Location: ...')`：发送 HTTP 重定向头。
- `exit`：立即终止脚本，否则后面的代码还会继续执行。

---

## 8. 引入文件：require / include

```php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/layout.php';
```

- `require`：文件不存在会报致命错误并停止；`include` 只警告。
- `require_once` / `include_once`：保证只引入一次，防止重复定义函数。
- `__DIR__`：当前文件所在目录的绝对路径，避免相对路径在不同入口下找不到文件。

---

## 9. 超全局数组：$_POST / $_GET / $_SERVER

| 变量        | 含义                                |
| ----------- | ----------------------------------- |
| `$_POST`    | HTTP POST 表单提交的参数            |
| `$_GET`     | URL query string 参数               |
| `$_SERVER`  | 服务器与请求信息                    |

```php
$xh = trim($_POST['xh'] ?? '');          // 表单提交的学号
$q  = trim($_GET['q'] ?? '');            // 搜索框输入
if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }  // 判断是否 POST 请求
```

> `edit_student.php` / `delete_student.php` 用 `REQUEST_METHOD` 区分“第一次打开页面（GET 展示表单）”和“提交表单（POST 执行更新）”。

---

## 10. 连接 MySQL：mysqli 面向对象风格

```php
$conn = new mysqli('localhost', 'root', '', 'xsgl');  // 主机 用户名 密码 库名
if ($conn->connect_error) {
    die('数据库连接失败：' . $conn->connect_error);   // die = echo + exit
}
$conn->set_charset('utf8mb4');                        // 防中文乱码
```

> 例子来自 `includes/db.php`。`->` 是对象访问成员的运算符（类似其他语言的 `.`）。

---

## 11. 预处理语句（防 SQL 注入，最重要！）

永远不要把用户输入直接拼进 SQL 字符串。本项目用 `?` 占位 + `bind_param` 绑定。

### 11.1 INSERT

```php
$stmt = $conn->prepare('INSERT INTO xs (xh, xm, xb, csrq, loan_price) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('ssisd', $xh, $xm, $xb, $csrq, $loan_price);
$stmt->execute();
$stmt->close();
```

`bind_param` 第一个参数是类型字符串，每个字符对应一个变量：

| 字符 | 类型     |
| ---- | -------- |
| `s`  | string   |
| `i`  | integer  |
| `d`  | double   |
| `b`  | blob     |

所以 `'ssisd'` 表示：`$xh` 字符串、`$xm` 字符串、`$xb` 整数、`$csrq` 字符串、`$loan_price` 浮点。

### 11.2 SELECT + 取结果

```php
$stmt = $conn->prepare('SELECT xh, xm, xb, csrq, loan_price FROM xs WHERE xh = ?');
$stmt->bind_param('s', $xh);
$stmt->execute();
$result = $stmt->get_result();        // 拿到结果集
$student = $result->fetch_assoc();    // 取一行（关联数组）
$stmt->close();
```

### 11.3 UPDATE / DELETE

```php
$stmt = $conn->prepare('UPDATE xs SET xm = ?, xb = ?, csrq = ?, loan_price = ? WHERE xh = ?');
$stmt->bind_param('sisds', $xm, $xb, $csrq, $loan_price, $xh);
$stmt->execute();

$stmt = $conn->prepare('DELETE FROM xs WHERE xh = ?');
$stmt->bind_param('s', $xh);
$stmt->execute();
```

### 11.4 LIKE 模糊查询

```php
$like = '%' . $q . '%';
$stmt = $conn->prepare('SELECT ... FROM xs WHERE xh LIKE ? OR xm LIKE ? ORDER BY xh');
$stmt->bind_param('ss', $like, $like);   // 通配符要拼进变量里，不能放 SQL 里
```

---

## 12. 直接执行无参数 SQL：`$conn->query()`

当 SQL 没有用户输入参数时，可以跳过 prepare，直接 `query`：

```php
$result = $conn->query('SELECT xh, xm, xb, csrq, loan_price, average_grade FROM xs ORDER BY xh');

// 多行统计
$studentStats = $conn->query('SELECT s.xh, s.xm, COUNT(x.cj) AS graded_count,
                                      ROUND(AVG(x.cj), 2) AS calc_avg
                               FROM xs s
                               LEFT JOIN xk x ON s.xh = x.xh
                               GROUP BY s.xh, s.xm');
```

只有 SELECT 这种“整句 SQL 写死、没有任何用户输入”的情况才能用 `query`。

---

## 13. 遍历结果集渲染表格

这是本项目最典型的模板模式：

```php
<?php if ($result->num_rows === 0): ?>
  <tr><td colspan="7" class="muted">没有匹配的学生。</td></tr>
<?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= h($row['xh']) ?></td>
    <td><?= h($row['xm']) ?></td>
    <td><?= $row['xb'] == 0 ? '女' : '男' ?></td>
    <td><a href="edit_student.php?xh=<?= urlencode($row['xh']) ?>">编辑</a></td>
  </tr>
<?php endwhile; ?>
```

- `num_rows`：结果集行数，判断是否为空。
- `fetch_assoc()`：循环取行。
- `urlencode()`：把学号编进 URL，避免特殊字符（如空格、中文）破坏链接。

---

## 14. 收尾：free / close

```php
$result->free();   // 释放结果集内存
$stmt->close();    // 关闭预处理
$conn->close();    // 关闭数据库连接
```

不是强制，但养成释放资源的习惯，尤其在长连接或大结果集时。

---

## 15. 表单回显与下拉框选中

`add_score.php` 里展示了一个常见模式：表单提交失败时保留用户已填的值，并在下拉框里选中之前选的项。

```php
<?php while ($s = $students->fetch_assoc()): ?>
  <option value="<?= h($s['xh']) ?>"<?= $xh_value === $s['xh'] ? ' selected' : '' ?>>
    <?= h($s['xm']) ?>（<?= h($s['xh']) ?>）
  </option>
<?php endwhile; ?>
```

`<?= ... ? ' selected' : '' ?>` 这种写法：条件成立就输出 `selected` 属性，否则输出空串。下拉框就会自动选中。

---

## 16. PRG 模式（Post / Redirect / Get）

避免用户刷新页面导致重复提交，本项目 edit/delete 操作成功后总是跳转一次：

```php
if ($stmt->execute()) {
    redirect_with_msg('list_students.php', '学生 ' . $xh . ' 已更新');
}
```

`redirect_with_msg` 把提示信息放到 URL 的 `msg=` 参数里，跳转后的列表页用 `$_GET['msg']` 取出并显示：

```php
$msg = $_GET['msg'] ?? '';
render_message($msg);   // 在 layout.php 里定义，非空就 echo 出来
```

---

## 17. 把 HTML 抽成公共布局

`includes/layout.php` 把每个页面都要写的 `<head>`、导航栏、`</body>` 封装成函数：

```php
render_header('学生管理', 'students');   // 输出 <head> + 导航 + <h2>
// ... 页面主体 ...
render_footer();                          // 输出 </div></body></html>
```

这样每个业务页面只需关心自己的表格/表单，不必重复写一大段 HTML 头。

---

## 18. 一图总结：单个页面的典型结构

以 `add_student.php` 为例：

```
1. require 公共文件（db + layout）
2. 从 $_POST 读表单参数          ← $xh = trim($_POST['xh'] ?? '');
3. 校验输入                      ← if ($xh === '') { ... exit; }
4. 类型转换 / 默认值             ← $xb = ... ? 0 : -1;
5. prepare + bind_param + execute ← INSERT/UPDATE/DELETE
6. render_header() 输出页面头
7. 根据执行结果 echo 成功/失败提示
8. render_footer() 输出页面尾
9. close 释放资源
```

其它列表页（list_*.php）只是把第 5 步换成 SELECT，第 7 步换成 while 循环渲染表格。掌握这一套，整个项目的所有文件都能读懂。

---

## 附：本项目用到的 PHP 函数速查

| 函数                                | 作用                                  |
| ----------------------------------- | ------------------------------------- |
| `trim($s)`                          | 去首尾空白                            |
| `htmlspecialchars($s, ...)`         | HTML 转义，防 XSS                      |
| `urlencode($s)`                     | URL 编码                              |
| `strpos($s, $sub)`                  | 查子串位置                            |
| `number_format($n, 2)`              | 格式化小数                            |
| `is_numeric($s)`                    | 是否为合法数字字符串                  |
| `header('Location: ...')`           | 发送 HTTP 头                           |
| `die($msg)` / `exit`                | 输出后终止 / 直接终止                 |
| `echo $x` / `print`                 | 输出                                  |
| `(float)$x` `(string)$x` (int)$x`   | 类型转换                              |
| `new mysqli(...)`                   | 连接数据库                            |
| `$conn->prepare / query`            | 预处理 / 直接查询                     |
| `$stmt->bind_param / execute`       | 绑定参数 / 执行                       |
| `$result->fetch_assoc / num_rows`   | 取行 / 行数                           |
| `$result->free / $stmt->close`      | 释放                                  |
