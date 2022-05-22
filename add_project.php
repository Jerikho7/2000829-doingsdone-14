<?php
require_once('helpers.php');
require_once('init.php');
if (!isset($user_id)) {
	header("Location: index.php");
	exit();
}

$project_name = filter_input(INPUT_GET, 'name');
$projects_names = [];
$projects = projects_db($connect, $user_id);
$projects_names = array_column($projects, 'name');

$page_content = include_template('add_project.php', ['projects' => $projects]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$errors = [];
	
	$rules = [
		'name' => function($value) use ($projects_names) {
			return valid_project_name($value, $projects_names);
		},
	];
	
	$project = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT], true);
	
	foreach ($project as $key => $value) { 
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
	}
	$errors = array_filter($errors);

	$project['user_id'] = $user_id;

	if (count($errors)) {
		$page_content = include_template(
			'add_project.php',
			[
				'projects' => $projects,
        		'project_name' => $project_name,
				'project' => $project,
				'errors' => $errors,
			]
		);
	} else {
		$sql = 'INSERT INTO projects (name, user_id) VALUES (?, ?)';
		$stmt = db_get_prepare_stmt($connect, $sql, $project);
		if ($stmt === false) {
			report_error(mysqli_error($connect));
		}
		if (!mysqli_stmt_execute($stmt)) {
			report_error(mysqli_error($connect));
		}
		header("Location: index.php");
	}	
};
$layout_content = include_template(
	'layout.php',
	[
		'content' => $page_content,
		'title' => 'Дела в порядке',
		'user' => $user_name,
	]
);
print($layout_content);
