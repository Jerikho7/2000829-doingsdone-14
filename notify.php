<?php
require_once('init.php');
require_once('helpers.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

$dsn = 'smtp://water-lily77:qiwi2000FAKE@smtp.mail.ru:143';
$transport = Transport::fromDsn($dsn);

$users = users_db($connect);

foreach ($users as $user) {
   
    $date = date('d-m-Y');
    $user_id = $user['id'];
    $tasks = get_deadline_tasks($connect, $user_id);
    $tasks_names = array_column($tasks, 'name');
    if ($tasks === 0) {
        exit;
    } else {
        $task_count = count($tasks_names);
        $all_tasks = implode(', ', $tasks_names);
        $plan = get_noun_plural_form($task_count, 'запланирована', 'запланированы', 'запланированы');
        $task = get_noun_plural_form($task_count, 'задача', 'задачи', 'задачи');
    
        $massage = new Email();
        $massage->to($user['email']);
        $massage->from("keks@phpdemo.ru");
        $massage->subject("Уведомление от сервиса «Дела в порядке»");
        $message->text('Уважаемый(ая), ' . $user['name'] . '. У вас ' . $plan . $task . $all_tasks . ' на ' . $date . '.');
    
        $mailer = new Mailer($transport);
        $mailer->send($message);
    }
}
