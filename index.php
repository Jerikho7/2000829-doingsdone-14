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
$project_id = filter_input(INPUT_GET, 'id');

$search = get_search_parameter($connect);
$massage = '';

if ($search) {
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

$task_id = filter_input(INPUT_GET, 'task_id');
$checked = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_SPECIAL_CHARS);

if ($checked) {
	$sql = 'UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?';
	$stmt = mysqli_prepare($connect, $sql);
	if ($stmt === false) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_bind_param($stmt, 'iii', $checked, $task_id, $user_id)) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_execute($stmt)) {
		report_error(mysqli_error($connect));
	}
}
$show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_SPECIAL_CHARS);

$today = filter_input(INPUT_GET, 'today', FILTER_SANITIZE_SPECIAL_CHARS);
if ($today) {
    $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at = CURDATE() AND user_id = ?';
    $stmt = mysqli_prepare($connect, $sql);
    if ($stmt === false) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_execute($stmt)) {
        report_error(mysqli_error($connect));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        report_error(mysqli_error($connect));
    }
    $tasks =  mysqli_fetch_all($result, MYSQLI_ASSOC);
}
$tomorrow = filter_input(INPUT_GET, 'tomorrow', FILTER_SANITIZE_SPECIAL_CHARS);
if ($tomorrow) {
    $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at = DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND user_id = ?';
    $stmt = mysqli_prepare($connect, $sql);
    if ($stmt === false) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_execute($stmt)) {
        report_error(mysqli_error($connect));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        report_error(mysqli_error($connect));
    }
    $tasks =  mysqli_fetch_all($result, MYSQLI_ASSOC);
}
$overdue = filter_input(INPUT_GET, 'overdue', FILTER_SANITIZE_SPECIAL_CHARS);
if ($overdue) {
    $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at < CURDATE() AND user_id = ?';
    $stmt = mysqli_prepare($connect, $sql);
    if ($stmt === false) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_execute($stmt)) {
        report_error(mysqli_error($connect));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        report_error(mysqli_error($connect));
    }
    $tasks =  mysqli_fetch_all($result, MYSQLI_ASSOC);
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
		'today' => $today,
		'tomorrow' => $tomorrow,
		'overdue' => $overdue,
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
