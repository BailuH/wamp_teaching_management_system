<?php
session_start();
require __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT password FROM user WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row && $row['password'] === $password) {
        $_SESSION['xh'] = $username;
        header('Location: student.php');
        exit;
    }
    header('Location: login.html?err=1');
    exit;
}
header('Location: login.html');
exit;
