<?php
require_once('init.php');
require_once('helpers.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

$transport = Transport::fromDsn('smtp://dd8ecd6ba862b6:c67ceaa24b2e1a@smtp.mailtrap.io:2525');

$mailer = new Mailer($transport);

$users = users_db($connect);
$date = date('d-m-Y');

foreach ($users as $user) {
    $user_id = $user['id'];
    $tasks = get_deadline_tasks($connect, $user_id);
       
    if (!empty($tasks)) {
        $tasks_names = array_column($tasks, 'name');
        $task_count = count($tasks_names);
        $all_tasks = implode(', ', $tasks_names);
        $plan = get_noun_plural_form($task_count, 'запланирована', 'запланированы', 'запланированы');
        $task = get_noun_plural_form($task_count, 'задача', 'задачи', 'задачи');
    
        $message = new Email();
        $message->to($user['email']);
        $message->from("keks@phpdemo.ru");
        $message->subject("Уведомление от сервиса «Дела в порядке»");
        $message->text("Уважаемый(ая), ${user['name']}. У вас $plan $task $all_tasks на $date.");
    
        $result = $mailer->send($message); 
        if (!$result) {
            echo "Сообщение не отправлено ${user['email']}! ";
        } else {
            echo "Сообщение отправлено ${user['email']}";
        }
    }
}
