<?php

require_once('init.php');
require_once('helpers.php');


$show_complete_tasks = rand(0, 1);

if (!$link) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}
else {
    $sql_projects = 'SELECT `id`, `name` FROM projects WHERE user_id = 1';
    $result = mysqli_query($link, $sql_projects);
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    $sql_tasks = 'SELECT status, t.name, deadline_at, p.name FROM tasks t JOIN projects p on t.project_id = p.id WHERE p.user_id = 1';
    $res = mysqli_query($link, $sql_tasks);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $page_content = include_template('main.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks
    ]);
};


function count_task ($tasks, $project) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project === $task['project_id']) {
            $count ++;
        }
    }
    return $count;
}
date_default_timezone_set('Asia/Sakhalin');
function task_deadline ($date) {
    if ($date === null) {
        return false;
    } 
    $cur_date = strtotime(date('d.m.Y'));
    $date_task = strtotime($date);
    $hours_count = floor(($cur_date - $date_task) / 3600);
    return $hours_count < 24;
}
        




$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'user' => 'Jerikho'
]);

print($layout_content);