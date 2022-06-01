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
function is_date_valid($date)
{
    if (is_null($date)) {
        return true;
    }
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);
    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Проверяет валидность введенной даты в форме
 * Отсутствие пустой формы и даты не позднее текущей
 *
 * @param string $date Дата в виде строки
 *
 * @return null при корректной введенной дате, иначе вывод ошибки
 */
function valid_date($date)
{
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

/**
 * Проверка выбранного проекта на существование в категории
 *
 * @param int $id id проекта
 * @param array $allowed_list массив со списком проектов пользователя
 *
 * @return null при корректной введенном проекте, иначе вывод ошибки
 */
function valid_projects($id, $allowed_list)
{
    if (empty($id)) {
        return 'Это поле должно быть заполнено';
    }
    if (!in_array($id, $allowed_list)) {
        return 'Проект не найден';
    }
    return null;
}

/**
 * Проверка имени проекта
 *
 * @param string $name введенное имя
 * @param array $allowed_list массив с именами проектов пользователя
 *
 * @return null при корректной введенном имени, иначе вывод ошибки
 */
function valid_project_name($name, $allowed_list)
{
    if (empty($name)) {
        return 'Это поле должно быть заполнено';
    }
    if (in_array($name, $allowed_list)) {
        return 'Проект с этим названием уже существует';
    }
    return null;
}

/**
 * Проверка заполненности формы
 *
 * @param string $name введенное имя
 *
 * @return null при корректно заполненной форме, иначе вывод ошибки
 */
function required($name)
{
    if (empty($name)) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * Проверка email
 *
 * @param string $email введенный email
 * @param array $allowed_list массив с email пользователей
 *
 * @return null при корректной введенном email, иначе вывод ошибки
 */
function valid_email($email, $allowed_list)
{
    if (empty($email)) {
        return 'Это поле должно быть заполнено';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email должен быть корректным';
    }
    if (in_array($email, $allowed_list)) {
        return 'Пользователь с этим email уже зарегистрирован';
    }
    return null;
}

/**
 * Проверка длины введеной строки с минимально и максимально допустимым количеством символов
 *
 * @param string $value введенная строка
 * @param int $min минимальное значение
 * @param int $max максимальное значение
 *
 * @return null при корректной введенной длины строки, иначе вывод ошибки
 */

function valid_lenght($value, $min, $max)
{
    if (empty($value)) {
        return 'Это поле должно быть заполнено';
    }
    $lenght = strlen($value);
    if ($lenght < $min || $lenght > $max) {
        return "Значение должно быть от $min до $max символов";
    }
    return null;
}

/**
 * Проверка email на входе
 *
 * @param string $email введенная строка
 *
 * @return null при корректной введенном email, иначе вывод ошибки
 */

function valid_auth_email($email)
{
    if (empty($email)) {
        return 'Это поле должно быть заполнено';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email должен быть корректным';
    }
    return null;
}

/**
 * Сохранние значения полей формы после валидации
 *
 * @param string $name данные заполненной формы
 *
 * @return string значение формы
 */
function get_post_val($name)
{
    return filter_input(INPUT_POST, $name);
}

/**
 * Отправление данных обратно в базу данных для исполнени или вывод ошибки
 *
 * @param $connect mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param string $value Данные для вставки на место плейсхолдеров
 *
 * @return void
 */
function execute_or_error($connect, $sql, $value) : void
{
    $stmt = db_get_prepare_stmt($connect, $sql, $value);
    if ($stmt === false) {
        report_error(mysqli_error($connect));
    }
    if (!mysqli_stmt_execute($stmt)) {
        report_error(mysqli_error($connect));
    }
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
function db_get_prepare_stmt($link, $sql, $data = [])
{
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
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
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
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
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

/**
 * Получение запроса на выполнение поиска
 *
 * @return string значение введеное в форму
 */
function get_search_parameter() : ? string
{
    $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
    if ($search === null) {
        return null;
    }
    return trim($search);
}

/**
 * Изменение статуса задачи у пользователя SQL-запроса
 *
 * @param $connect mysqli Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return void
 */
function change_status($connect, $user_id) : void
{
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

/**
 * Выполнение посика на основе SQL-запроса
 *
 * @param $connect mysqli Ресурс соединения
 * @param int $user_id id пользователя
 * @param string $search полученное значение
 *
 * @return array результат поиска в виде массива
 */
function search($connect, $user_id, $search)
{
    $sql = <<<'EOT'
    SELECT t.id, status, t.name, file, deadline_at, p.id
    FROM tasks t JOIN projects p on p.id = t.project_id
    WHERE p.user_id = ? AND MATCH(t.name) AGAINST(?)
    EOT;
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

/**
 * Выполнение фильтрации на основе SQL-запроса
 *
 * @param $connect mysqli Ресурс соединения
 * @param string $filter полученное значение
 * @param int $user_id id пользователя
 *
 * @return array результат поиска в виде массива
 */
function filter($connect, $filter, $user_id)
{
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
            header('Location: index.php');
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

/**
 * Подключение к БД
 *
 * @param array $db массив с данным для подключения к БД
 *
 * @return $link соединение
 */
function db_connect($db)
{
    $link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

    if (!$link) {
        return false;
    }
    mysqli_set_charset($link, 'utf8');
    return $link;
}

/**
 * Получение списка пользователей
 *
 * @param $connect mysqli Ресурс соединения
 *
 * @return array массив списка пользователей
 */
function users_db($connect)
{
    $sql = 'SELECT id, email, name FROM users';
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        report_error(mysqli_error($connect));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получение проектов данного пользователя
 *
 * @param $connect mysqli Ресурс соединения
 * @param int $user id пользователя
 *
 * @return array массив списка проектов
 */
function projects_db($connect, $user)
{
    $sql = <<<'EOT'
    SELECT p.id, p.name, COUNT(project_id) task_count FROM projects p
    LEFT JOIN tasks t ON p.id = t.project_id WHERE p.user_id = ?
    GROUP BY p.id ORDER BY p.name asc
    EOT;
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

/**
 * Получение и подсчет задач в каждом проекте у данного пользователя
 *
 * @param $connect mysqli Ресурс соединения
 * @param int $user_id id пользователя
 * @param int $project_id id проекта
 *
 * @return array массив списка проектов
 */
function tasks_db($connect, $project_id, $user_id)
{
    if ($project_id) {
        $sql_tasks = <<<'EOT'
        SELECT id, status, name, deadline_at, file, project_id FROM tasks
        WHERE user_id = ? AND project_id = ?
        EOT;
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

/**
 * Получение задача с deadline у данного пользователя
 *
 * @param $connect mysqli Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return array массив списка проектов
 */
function get_deadline_tasks($connect, $user_id)
{
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

/**
 * Подключние ошибки
 *
 * @param string $error
 *
 * @return void
 */
function report_error($error) : void
{
    $page_content = include_template('error.php', ['error' => $error]);

    print include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Дела в порядке',
        'user' => 'Евгения'
    ]);
    exit;
}

/**
 * Подключние ошибки 404
 *
 * @param string $error
 *
 * @return void
 */
function report_error_404($error_404) : void
{
    $page_content = include_template('error_404.php', ['error_404' => $error_404]);

    print include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Дела в порядке',
        'user' => 'Евгения',
    ]);
    exit;
}

/**
 * Опрделение является ли дата меньше 24 часов
 *
 * @param int $date дата
 *
 * @return array массив списка проектов
 */
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
function include_template($name, array $data = [])
{
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
