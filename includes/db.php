<?php
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'xsgl');
if ($conn->connect_error) {
    die('数据库连接失败：' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_msg($url, $msg) {
    $sep = strpos($url, '?') === false ? '?' : '&';
    header('Location: ' . $url . $sep . 'msg=' . urlencode($msg));
    exit;
}
