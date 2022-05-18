<?php
require_once('helpers.php');
require_once('init.php');

if (!isset($user_id)) {
	$page_content = include_template('guest.php');
	$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке',]);
	print($layout_content);
	exit();
}

$projects = [];
$tasks = [];
$page_content = '';
$show_complete_tasks = rand(0, 1);	
$projects = projects_db($connect, $user_id);
$project_id = filter_input(INPUT_GET, 'id');

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$massage = '';

if (isset($search)) {
	$search = trim($search);
	$sql = 'SELECT t.id, status, t.name, file, deadline_at, p.id '
		. 'FROM tasks t JOIN projects p on p.id = t.project_id '
		. 'WHERE p.user_id = ? AND MATCH(t.name) AGAINST(?)';
	$stmt = mysqli_prepare($connect, $sql);
	if ($stmt === false) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_bind_param($stmt, 'is', $user_id, $search)) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_execute($stmt)) {
		report_error(mysqli_error($connect));
	}
	$result = mysqli_stmt_get_result($stmt);
	if (!$result) {
		report_error(mysqli_error($connect));
	}
	$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
	if (count($tasks) === 0) {
		$massage = 'Ничего не найдено по вашему запросу';
	}
} else {
	$tasks = tasks_db($connect, $project_id, $user_id);
	if (count($tasks) === 0) {
		report_error_404('в выбранной категории нет задач');
	};
}

$page_content = include_template(
	'main.php',
	[
		'projects' => $projects,
		'project_id' => $project_id,
		'tasks' => $tasks,
		'show_complete_tasks' => $show_complete_tasks,
		'massage' => $massage,
		'search' => $search,
	]
);

$layout_content = include_template(
	'layout.php',
	[
		'content' => $page_content,
		'title' => 'Дела в порядке',
		'user' => $user_name,
	]
);

print($layout_content);
