<?php
require_once('helpers.php');
require_once('init.php');


$project_id = filter_input(INPUT_GET, 'id');
$projects_ids = [];
$projects = projects_db($connect, $user_id);
$projects_ids = array_column($projects, 'id');

$page_content = include_template('add.php', ['projects' => $projects]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$required = ['name', 'project_id'];
	$errors = [];
	
	$rules = [
		'name' => function($value) {
			return required($value);
		},
		'project_id' => function($value) use ($projects_ids) {
			return valid_projects($value, $projects_ids);
		},
		'deadline_at' => function($value) {
			return valid_date($value);
		},
	];
	
	$task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project_id' => FILTER_DEFAULT, 'deadline_at' => FILTER_DEFAULT], true);
	if (empty($task['deadline_at'])){
		$task['deadline_at'] = null;
	}
	foreach ($task as $key => $value) { 
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
	}
	$errors = array_filter($errors);

	$task['user_id'] = $user_id;
	
	if (!empty($_FILES['file']['name'])) {
		$file_name = $_FILES['file']['name']; 
		$file_path = __DIR__ . '/uploads/';
		$file_url = '/uploads/' . $file_name;
		$task['file'] = $file_url;
		move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
	} else {
		$task['file'] = null;
	};

	if (count($errors)) {
		$page_content = include_template(
			'add.php',
			[
				'projects' => $projects,
        		'project_id' => $project_id,
				'task' => $task,
				'errors' => $errors,
			]
		);
	} else {
		$sql = 'INSERT INTO tasks (created_at, name, project_id, deadline_at, user_id, file) VALUES (NOW(), ?, ?, ?, ?, ?)';
		$stmt = db_get_prepare_stmt($connect, $sql, $task);
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
		'user' => 'Евгения',
	]
);
print($layout_content);
