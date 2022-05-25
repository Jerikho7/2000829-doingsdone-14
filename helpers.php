<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid($date) {
    if (is_null($date)) {
        return true;
    }
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);
    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;   
}
function valid_date($date) {
    if (is_null($date)) {
        return null;
    }
    if (!is_date_valid($date)) {
        return 'Это поле заполненно не корректно';
    }
    if ($date < date('Y-m-d')) {
        return 'Дата выполнения задачи не должна быть позднее текущего дня';
    }
    return null;	
}
//проверка выбранного проекта на существование в категории
function valid_projects($id, $allowed_list) {
    if (empty($id)) {
        return 'Это поле должно быть заполнено';
    } 
    if (!in_array($id, $allowed_list)) {
        return 'Проект не найден';
    }
    return null;
}
//проверка имени проекта
function valid_project_name($name, $allowed_list) {
    if (empty($name)) {
        return 'Это поле должно быть заполнено';
    } 
    if (in_array($name, $allowed_list)) {
        return 'Проект с этим названием уже существует';
    }
    return null;
}
//проверка заполненности
function required($name) {
    if (empty($name)) {
        return 'Это поле должно быть заполнено';
    } 
    return null;
}
//проверкка email
function valid_email($email, $allowed_list) {
    if (empty($email)) {
        return 'Это поле должно быть заполнено';  
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email должен быть корректным';
    }
    if (in_array($email, $allowed_list)){
        return 'Пользователь с этим email уже зарегистрирован';
    }
    return null;
}
//проверка длины 
function valid_lenght($value, $min, $max) {
    if (empty($value)) {
        return 'Это поле должно быть заполнено';  
    }
    $lenght = strlen($value);
    if ($lenght < $min || $lenght > $max) {
        return "Значение должно быть от $min до $max символов";
    }
    return null;
}
//проверка email на входе
function valid_auth_email($email) {
    if (empty($email)) {
        return 'Это поле должно быть заполнено';  
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email должен быть корректным';
    }
    return null;
}

function get_post_val($name) {
    return filter_input(INPUT_POST, $name);
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

//получение запроса на поиск
function  get_search_parameter($connect) {
    $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
    if ($search === null) {
        return null;
    }
    return $search = trim($search);
}
//выполнено
function  change_status($connect, $user_id) {
    $task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $checked = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_SPECIAL_CHARS);
    switch ($checked) {
		case 0:
			$sql = 'UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?';
		    break;
		case 1:
			$sql = 'UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?';
		    break;
	}
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
//поиск
function search ($connect, $user_id, $search) {
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
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
//фильтр
function filter ($connect, $filter, $user_id) {
    switch ($filter) {
    case 'today':
        $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at = CURDATE() AND user_id = ?';
        break; 
    case 'tomorrow':
        $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at = DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND user_id = ?';
        break;
    case 'overdue':
        $sql = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE deadline_at < CURDATE() AND user_id = ?';
        break;
    default:
        header("Location: index.php");
        exit;
    }
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
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
//подключение к БД
function db_connect ($db) 
{
    $link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
   
    if (!$link) {
        return false;
    }
    mysqli_set_charset($link, 'utf8');
    return $link; 
}
//подключение списка пользователей
function users_db ($connect) {
    $sql = 'SELECT id, email, name FROM users';
    $result = mysqli_query($connect, $sql);
    if (!$result) {
	    report_error(mysqli_error($connect));
    }
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
//подключение проектов
function projects_db ($connect, $user) {
    $sql = 'SELECT p.id, p.name, COUNT(project_id) task_count FROM projects p '
				. 'LEFT JOIN tasks t ON p.id = t.project_id WHERE p.user_id = ? '
				. 'GROUP BY p.id ORDER BY p.name asc';
    $stmt = mysqli_prepare($connect, $sql);
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
    }
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
//подключение задач 
function tasks_db ($connect, $project_id, $user_id) {
    if ($project_id) {
        $sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks '
                    . 'WHERE user_id = ? AND project_id = ?';
        $stmt = mysqli_prepare($connect, $sql_tasks);
        if ($stmt === false) {
            report_error(mysqli_error($connect));
        }
        if (!mysqli_stmt_bind_param($stmt, 'ii', $user_id, $project_id)) {
            report_error(mysqli_error($connect));
        }
    } else {
        $sql_tasks = 'SELECT id, status, name, deadline_at, file, project_id FROM tasks WHERE user_id = ?';
        $stmt = mysqli_prepare($connect, $sql_tasks);
        if ($stmt === false) {
            report_error(mysqli_error($connect));
        }
        if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
            report_error(mysqli_error($connect));
        }
    }
    if (!mysqli_stmt_execute($stmt)) {
        report_error(mysqli_error($connect));
    }
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) {
        report_error(mysqli_error($connect));
    }
	return mysqli_fetch_all($res, MYSQLI_ASSOC);
}
//подключение задача с deadline
/*function get_deadline_tasks ($connect, $user_id) {
    $sql = 'SELECT name FROM tasks WHERE status = 0 AND deadline_at = CURDATE() AND user_id = ?';
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
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
*/
function get_deadline_tasks ($connect) {
    $sql = 'SELECT u.id, u.name as user_name, email, t.name as task_name '
            . 'FROM users u '
            . 'JOIN tasks t on u.id = t.user_id '
            . 'WHERE status = 0 AND deadline_at = CURDATE()';
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        report_error(mysqli_error($connect));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
//подключение ошибки
function report_error($error)
{
    $page_content = include_template('error.php', ['error' => $error]);
    
    print include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Дела в порядке',
        'user' => 'Евгения'
    ]);
    exit;
}
//подключение ошибки 404
function report_error_404($error_404)
{
    $page_content = include_template('error_404.php', ['error_404' => $error_404]);
    
    print include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Дела в порядке',
        'user' => 'Евгения',
    ]);
    exit;
}

//определение дедлайна
function task_deadline($date)
{
	if ($date === null) {
		return false;
	}
	$cur_date = strtotime(date('d-m-Y'));
	$date_task = strtotime($date);
	$hours_count = abs(floor(($cur_date - $date_task) / 3600));
	return $hours_count < 24;
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}
