<?php
require_once('helpers.php');
require_once('init.php');


//$page_content = include_template('auth.php', ['auth' => $auth]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$auth = $_POST;
    $required = ['email', 'password'];
	$errors = [];

    $rules = [
		'email' => function($value) {
			return valid_auth_email($value);
		},
		'password' => function($value) {
			return valid_auth_password($value);
		},
	];

    $auth = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);
	
	foreach ($auth as $key => $value) { 
		if (isset($rules[$key])) {
			$rule = $rules[$key];
			$errors[$key] = $rule($value);
		}
	}
	$errors = array_filter($errors);

	$email = mysqli_real_escape_string($connect, $auth['email']);
	$sql = 'SELECT * FROM users WHERE email = "$email"';
	$result = mysqli_query($connect, $sql);
	if (!$result) {
		report_error(mysqli_error($connect));
	}

	$user = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;

	if (!count($errors) and isset($user)) {
		if (password_verify($auth['password'], $user['password'])) {
			$_SESSION['user'] = $user;	
		} else {
			$errors['password'] = 'Неверный пароль';
		}
	} else {
		$errors['email'] = 'Такой пользователь не найден';
	}
	
	if (count($errors)) {
		$page_content = include_template(
			'auth.php',
			[
				'auth' => $auth,
				'errors' => $errors,
			]
		);
	} else {
		header("Location: index.php");
		exit();
	}
} else {
	$page_content = include_template('auth.php', []);

	if (isset($_SESSION['user'])) {
		header("Location: index.php");
		exit();
	}

}

$layout_content = include_template(
	'layout.php',
	[
		'content' => $page_content,
		'title' => 'Дела в порядке',
		'user' => '',
	]
);
print($layout_content);
