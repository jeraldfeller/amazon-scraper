<?php
//require '../Model/Init.php';
require '/var/www/html/am/Model/Init.php';
require ROOT . '/Model/Scraper.php';

$scraper = new Scraper();
switch ($_GET['action']) {
  case 'get-asin':
    $limit = 1;
    $table = 'asin_tbl';
    $data = $scraper->getAsinNew($table, $limit);
    echo json_encode($data);
    break;
}

?>
