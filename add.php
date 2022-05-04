<?php
require_once('helpers.php');
$db = require_once('db.php');

$connect = db_connect($db);
if (!$connect) {
	report_error(mysqli_connect_error());
};
$user = 2;
$project_id = filter_input(INPUT_GET, 'id');
$sql_projects = 'SELECT p.id, p.name, COUNT(project_id) task_count FROM projects p '
				. 'LEFT JOIN tasks t ON p.id = t.project_id WHERE p.user_id = ? '
				. 'GROUP BY p.name ORDER BY p.name asc';
$stmt = mysqli_prepare($connect, $sql_projects);
if ($stmt === false) {
	report_error(mysqli_error($connect));
}
if (!mysqli_stmt_bind_param($stmt, 'i', $user)) {
	report_error(mysqli_error($connect));
}
if (!mysqli_stmt_execute($stmt)) {
	report_error(mysqli_error($connect));
}
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
	report_error(mysqli_error($connect));
} else {
	$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
};

$page_content = include_template(
	'add.php',
	[
		'projects' => $projects,
        'project_id' => $project_id,
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