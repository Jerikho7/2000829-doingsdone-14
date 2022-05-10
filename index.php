<?php
date_default_timezone_set('Asia/Sakhalin');
require_once('helpers.php');
$db = require_once('db.php');

$connect = db_connect($db);
if (!$connect) {
	report_error(mysqli_connect_error());
};
$projects = [];
$tasks = [];
$page_content = '';
$show_complete_tasks = rand(0, 1);
$user = 2;

$projects = projects_db($connect, $user);

$project_id = filter_input(INPUT_GET, 'id');
if ($project_id) {
	$sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks '
				. 'WHERE user_id = ? AND project_id = ?';
	$stmt = mysqli_prepare($connect, $sql_tasks);
	if ($stmt === false) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_bind_param($stmt, 'ii', $user, $project_id)) {
		report_error(mysqli_error($connect));
	}
} else {
	$sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE user_id = ?';
	$stmt = mysqli_prepare($connect, $sql_tasks);
	if ($stmt === false) {
    	report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_bind_param($stmt, 'i', $user)) {
    	report_error(mysqli_error($connect));
	}
}
if (!mysqli_stmt_execute($stmt)) {
	report_error(mysqli_error($connect));
}
$res = mysqli_stmt_get_result($stmt);
if (!$res) {
	report_error(mysqli_error($connect));
} else {
	$tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
} 
if (count($tasks) === 0) {
	report_error_404('в выбранной категории нет задач');
};
$page_content = include_template(
	'main.php',
	[
		'projects' => $projects,
		'project_id' => $project_id,
		'tasks' => $tasks,
		'show_complete_tasks' => $show_complete_tasks,
	]
);

$layout_content = include_template(
	'layout.php',
	[
		'content' => $page_content,
		'title' => 'Дела в порядке',
		'user' => 'Евгения',
	]
);

print($layout_content);