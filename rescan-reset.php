<?php
set_time_limit(0);
//require '../Model/Init.php';
require '/var/www/html/am/amazon-scraper/Model/Init.php';
require ROOT . '/Model/Scraper.php';

$scraper = new Scraper();

$scraper->resetRescan();

?>

