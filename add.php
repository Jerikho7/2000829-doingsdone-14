<?php
require_once('helpers.php');
$db = require_once('db.php');
$connect = db_connect($db);
if (!$connect) {
	report_error(mysqli_connect_error());
};
$user_id = 2;
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
			return valid_task_name($value);
		},
		'project_id' => function($value) use ($projects_ids) {
			return valid_projects($value, $projects_ids);
		},
		'deadline_at' => function($value) {
			return valid_date($value);
		},
	];
	
	$task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project_id' => FILTER_DEFAULT, 'deadline_at' => FILTER_DEFAULT], true);

	foreach ($task as $key => $value) { 
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
		if (in_array($key, $required) && empty($value)) { 
			$errors[$key] = "Поле $key должно быть заполнено корректно";
		}
	}
	$errors = array_filter($errors);

	$task += array('user_id' => $user_id);
	
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
		$result = mysqli_stmt_execute($stmt);
		if (!$result) {
			report_error(mysqli_error($connect));
		}
		if ($result) {
			header("Location: index.php");
		}
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