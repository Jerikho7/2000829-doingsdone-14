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
$projects = projects_db($connect, $user_id);
$project_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

$search = get_search_parameter($connect);
$massage = '';
$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
$show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_SPECIAL_CHARS);

$task_checked = change_status($connect, $user_id, $tasks);
	$tasks = tasks_db($connect, $project_id, $user_id);
	if (count($tasks) === 0) {
		report_error_404('в выбранной категории нет задач');
	};
if ($search) {
	$tasks = search($connect, $user_id, $search);
	if (count($tasks) === 0) {
		$massage = 'Ничего не найдено по вашему запросу';
	}
	$task_checked = change_status($connect, $user_id, $tasks);
} 
if ($filter) {
	$tasks = filter($connect, $filter, $user_id);
	$task_checked = change_status($connect, $user_id, $tasks);
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
		'filter' => $filter,
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
