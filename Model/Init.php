<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ob_start();
session_start();

define('DB_USER', 'root');
define('DB_PWD', 'malc0lm.d99');
define('DB_NAME', 'amz_url_tool');
define('DB_HOST', 'localhost');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME .'');

define('ROOT', '/var/www/html/am/amazon-scraper/');
//define('ROOT', '../')
?>
