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
$projects_ids =[];  //что это? пчм здесь?
if (!$result) {
	report_error(mysqli_error($connect));
} else {
	$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
	$projects_ids = array_column($projects, 'id');
};

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$required = ['name', 'project_id', 'deadline_at']; //  не уверена на счет deadline_at, так как 
	$errors = []; // массив с ошибками

	$rules = [
		'name' => function($value) {
			return valid_task_name($value);
		},
		'project_id' => function($value) use ($projects_ids) {
			return valid_projects($value, $projects_ids);
		},
		'deadline_at' => function($value) {
			return is_date_valid($value);
		}
	];
	
	$task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project_id' => FILTER_DEFAULT, 'deadline_at' => FILTER_DEFAULT], true);

	foreach ($task as $key => $value) { //обход и валидация
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
		if (in_array($key, $required)) {  // проверка на заполненность project_id где?
			$errors[$key] = "Поле $key должно быть заполнено корректно"; //сохраняет ошибку в массив
		}
	}
	$errors = array_filter($errors); // стирает все значения типа null 

	if (!empty($_FILES['file']['name'])) {
		$tmp_name = $_FILES['file']['tmp_name'];
		
		$file_path = __DIR__ . '/uploads/';
		$file_url = '/uploads/' . $tmp_name;

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($finfo, $tmp_name);
		/*if ($file_type!== $ -> какая-то переменная в которой функция или собраны типы файлов для загрузки) {
			$errors['file] = 'Формат для загрузки неприемлем'
		} else {
			move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $tmp_name);
		}	*/

		
	}
	
	
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
		//$sql = 


	};




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