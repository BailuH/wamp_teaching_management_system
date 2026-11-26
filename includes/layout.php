<?php
function render_header($title, $active = '') {
    $items = array(
        'home'     => array('hwq.html', '首页 / 录入'),
        'students' => array('list_students.php', '学生管理'),
        'teachers' => array('list_teachers.php', '教师管理'),
        'courses'  => array('list_courses.php', '课程管理'),
        'scores'   => array('list_scores.php', '选课成绩'),
        'stats'    => array('stats.php', '成绩统计'),
    );
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . h($title) . '</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<nav class="topnav">';
    foreach ($items as $key => $item) {
        $class = $key === $active ? ' class="active"' : '';
        echo '<a href="' . $item[0] . '"' . $class . '>' . h($item[1]) . '</a>';
    }
    echo '</nav>
<h2>' . h($title) . '</h2>';
}

function render_footer() {
    echo '</div>
</body>
</html>';
}

function render_message($msg) {
    if ($msg !== '') {
        echo '<p class="msg success">' . h($msg) . '</p>';
    }
}
