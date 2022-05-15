<?php
require_once('helpers.php');
require_once('init.php');

$auth = [];
$page_content = include_template('auth.php', ['auth' => $auth]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['email', 'password'];
	$errors = [];

    $rules = [
		'email' => function($value) {
			return valid_auth_email($value);
		},
		'password' => function($value) {
			return required($value);
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

	if (count($errors)) {
		$page_content = include_template('auth.php', ['auth' => $auth, 'errors' => $errors,]);
		$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке',]);
		print($layout_content);
		exit;
	}

	$email = $auth['email'];
	$sql = 'SELECT * FROM users WHERE email = ?';
	$stmt = mysqli_prepare($connect, $sql);
	if ($stmt === false) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_bind_param($stmt, 's', $email)) {
		report_error(mysqli_error($connect));
	}
	if (!mysqli_stmt_execute($stmt)) {
		report_error(mysqli_error($connect));
	}
	$result = mysqli_stmt_get_result($stmt);
	if (!$result) {
		report_error(mysqli_error($connect));
	} 
	$user = mysqli_fetch_array($result, MYSQLI_ASSOC);

	
	if (empty($user)) {
		$errors['email'] = 'Пользователь не найден';
		$page_content = include_template('auth.php', ['auth' => $auth, 'errors' => $errors,]);   
	} else {
		if (password_verify($auth['password'], $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
            ];
			header("Location: index.php");
        	exit();
        } else {
            $errors['password'] = 'Пароль введен не верно';
			$page_content = include_template('auth.php', ['auth' => $auth, 'errors' => $errors,]);
        }
	}
};

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке',]);
print($layout_content);
