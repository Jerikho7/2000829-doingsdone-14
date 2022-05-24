<?php
require_once('init.php');
require_once('helpers.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

$dsn = 'smtp://water-lily77:gCJ1tzwqFb6T8vc2Wakb@smtp.mail.ru:465';
$transport = Transport::fromDsn($dsn);

$users = users_db($connect);

foreach ($users as $user) {
    $date = date('d-m-Y');
    $user_id = $user['id'];
    $tasks = get_deadline_tasks($connect, $user_id);
    $tasks_names = array_column($tasks, 'name');
    $task_count = count($tasks_names);
    if ($task_count === 0) {
        exit;
    } else {
        $all_tasks = implode(', ', $tasks_names);
        $plan = get_noun_plural_form($task_count, 'запланирована', 'запланированы', 'запланированы');
        $task = get_noun_plural_form($task_count, 'задача', 'задачи', 'задачи');
    
        $message = new Email();
        $message->to($user['email']);
        $message->from("keks@phpdemo.ru");
        $message->subject("Уведомление от сервиса «Дела в порядке»");
        $message->text("Уважаемый(ая), ${user['name']}. У вас $plan $task $all_tasks на $date.");
    
        $mailer = new Mailer($transport);
        if (!$mailer->send($message)) {
            echo "Couldn't send email";
        } else {
            echo "Сообщение отправлено";
        }
    }
}
