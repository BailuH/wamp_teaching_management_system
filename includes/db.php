<?php
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'xsgl');
if ($conn->connect_error) {
    die('数据库连接失败：' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// h()函数的功能是用于将用户的输入进行一次转译，防止用户直接输入JS代码导致浏览器默认执行
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_msg($url, $msg) {
    // strpos是查找子串单位位置
    $sep = strpos($url, '?') === false ? '?' : '&';
    // `.` 是字符串连接符，类似python的`+`
    header('Location: ' . $url . $sep . 'msg=' . urlencode($msg));
    exit;
}
