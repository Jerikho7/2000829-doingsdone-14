<?php
require_once('helpers.php');
require_once('init.php');

$register = [];
$users = users_db($connect);
$emails = array_column($users, 'email');
$page_content = include_template('register.php', ['register' => $register]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$required = ['email', 'password', 'name'];
	$errors = [];
	
	$rules = [
		'email' => function($value) use ($emails) {
			return valid_email($value, $emails);
		},
		'password' => function($value) {
			return valid_lenght($value, 6, 12);
		},
		'name' => function($value) {
			return required($value);
		},
	];
	
	$register = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'name' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);
	
	foreach ($register as $key => $value) { 
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
	}
	$errors = array_filter($errors);

	if (count($errors)) {
		$page_content = include_template(
			'register.php',
			[
				'register' => $register,
				'errors' => $errors,
			]
		);
	} else {
		$password = password_hash($register['password'], PASSWORD_DEFAULT);
		$register['password'] = $password;
		$sql = 'INSERT INTO users (created_at, email, name, password) VALUES (NOW(), ?, ?, ?)';
		$stmt = db_get_prepare_stmt($connect, $sql, $register);
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
	]
);
print($layout_content);
