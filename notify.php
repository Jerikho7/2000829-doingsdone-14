<?php
require_once('init.php');
require_once('helpers.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

$transport = Transport::fromDsn('smtp://dd8ecd6ba862b6:c67ceaa24b2e1a@smtp.mailtrap.io:2525');

$mailer = new Mailer($transport);

$users_tasks = get_deadline_tasks($connect);
$date = date('d-m-Y');
if (isset($users_tasks)) {
    foreach ($users_tasks as $key => $value) {
        $message = new Email();
        $message->to($value['email']);
        $message->from("keks@phpdemo.ru");
        $message->subject("Уведомление от сервиса «Дела в порядке»");
        $message->text("Уважаемый(ая), ${value['user_name']}. У вас запланирована задача ${value['task_name']} на $date.");
    }
    if ($mailer->send($message)) {
        echo "Сообщение отправлено ${value['email']}";
    } else {
        echo "Сообщение не отправлено!";
    }
}

