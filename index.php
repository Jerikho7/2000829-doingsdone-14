<?php
require_once('helpers.php');
require_once('init.php');

if (!isset($user_id)) {
	$page_content = include_template('guest.php');
	$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке',]);
} else {
	$projects = [];
	$tasks = [];
	$page_content = '';
	$show_complete_tasks = rand(0, 1);	
	$projects = projects_db($connect, $user_id);
	

	$project_id = filter_input(INPUT_GET, 'id');
	
	if ($project_id) {
		$sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks '
					. 'WHERE user_id = ? AND project_id = ?';
		$stmt = mysqli_prepare($connect, $sql_tasks);
		if ($stmt === false) {
			report_error(mysqli_error($connect));
		}
		if (!mysqli_stmt_bind_param($stmt, 'ii', $user_id, $project_id)) {
			report_error(mysqli_error($connect));
		}
	} else {
		$sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE user_id = ?';
		$stmt = mysqli_prepare($connect, $sql_tasks);
		if ($stmt === false) {
    		report_error(mysqli_error($connect));
		}
		if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
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
			'user' => $user_name,
		]
	);
};



print($layout_content);
