<?php

require_once('helpers.php');
require_once('init.php');

$auth = [];
$page_content = include_template('auth.php', ['auth' => $auth]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['email', 'password'];
    $errors = [];

    $rules = [
        'email' => function ($value) {
            return valid_auth_email($value);
        },
        'password' => function ($value) {
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
        $error_message = 'Пожалуйста, исправьте ошибки в форме';
        $page_content = include_template('auth.php', ['auth' => $auth, 'errors' => $errors, 'error_message' => $error_message,]);
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

    if (empty($user) || !password_verify($auth['password'], $user['password'])) {
        $errors['email'] = '';
        $errors['password'] = '';
        $error_message = 'Вы ввели неверный email/пароль';
        $page_content = include_template('auth.php', ['auth' => $auth, 'errors' => $errors, 'error_message' => $error_message,]);
    } else {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
        ];
        header('Location: index.php');
        exit();
    }
};

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке',]);
print($layout_content);
