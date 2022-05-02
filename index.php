<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once('helpers.php');
$db = require_once('db.php');

$connect = db_connect($db);
//$connect = false;
if (!$connect) {
    report_error(mysqli_connect_error());
};

$projects = [];
$tasks = [];
$page_content = '';
$show_complete_tasks = rand(0, 1);
$user = 1;

$sql_projects = 'SELECT id, name FROM projects WHERE user_id = ?';
$stmt = mysqli_prepare($connect, $sql_projects);
if ($stmt === false) {
    report_error(mysqli_error($connect));
}
mysqli_stmt_bind_param($stmt, 'i', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    report_error(mysqli_error($connect));
}
else {
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC); 
};    
$sql_tasks = 'SELECT status, t.name, deadline_at, p.name as category FROM tasks t JOIN projects p on t.project_id = p.id WHERE p.user_id = ?';
$stmt = mysqli_prepare($connect, $sql_tasks);
if ($stmt === false) {
    report_error(mysqli_error($connect));
}
mysqli_stmt_bind_param($stmt, 'i', $user);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if (!$res) {
    report_error(mysqli_error($connect));
}
else {
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
};
$page_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks
    ]);
function count_task ($tasks, $project) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project['name'] === $task['category']) {
            $count ++;
        }
    }
    return $count;
}
date_default_timezone_set('Asia/Sakhalin');
function task_deadline ($date) {
    if ($date === null) {
        return false;
    } 
    $cur_date = strtotime(date('d-m-Y'));
    $date_task = strtotime($date);
    $hours_count = abs(floor(($cur_date - $date_task) / 3600));
    return $hours_count < 24;
}  
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'user' => 'Евгения'
]);
print($layout_content);