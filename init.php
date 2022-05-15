<?php
session_start();
if (isset($_SESSION['user'])) {
	$user_id = $_SESSION['user']['id'];
	$user_name = $_SESSION['user']['name'];
}

define('CACHE_DIR', basename(__DIR__ . DIRECTORY_SEPARATOR . 'cache'));
define('UPLOAD_PATH', basename(__DIR__ . DIRECTORY_SEPARATOR . 'uploads'));

date_default_timezone_set('Asia/Sakhalin');
$db = require_once('db.php');
$connect = db_connect($db);
if (!$connect) {
	report_error(mysqli_connect_error());
};
