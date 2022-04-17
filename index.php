<?php

require_once('helpers.php'); 

$show_complete_tasks = rand(0, 1);

$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];

$tasks = [
    [
        'name' => 'Собеседование в IT компании',
        'date' => '01.12.2019',
        'category' => 'Работа',
        'done' => false,
    ],
    [
        'name' => 'Выполнить тестовое задание',
        'date' => '25.12.2019',
        'category' => 'Работа',
        'done' => false,
    ],
    [
        'name' => 'Сделать адание первого раздела',
        'date' => '21.12.2019',
        'category' => 'Учеба',
        'done' => true,
    ],
    [
        'name' => 'Встреча с другом',
        'date' => '22.12.2019',
        'category' => 'Входящие',
        'done' => false,
    ],
    [
        'name' => 'Купить корм для кота',
        'date' => null,
        'category' => 'Домашние дела',
        'done' => false,
    ],
    [
        'name' => 'Заказать пиццу',
        'date' => null,
        'category' => 'Домашние дела',
        'done' => false,
    ],
];

function count_task ($tasks, $project) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project === $task['category']) {
            $count ++;
        }
    }
    return $count;
};


$page_content = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'projects' => $projects,
    'tasks'=> $tasks
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'user' => 'Jerikho'
]);

print($layout_content);
?>