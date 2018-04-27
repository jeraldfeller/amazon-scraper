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

  case 'update-asin':
    $data = $_GET;
    $scraper->updateAsinMain(array(
      'id' => $data['id'],
      'table' => $data['table'],
      'success' => $data['success'],
      'failed_message' => $data['failed_message']
      ));
    break;

  case 'insert-rescan':
    $data = $_GET;
    $id = $data['id'];
    $table = $data['table'];
    $asin = $data['asin'];
    $locale = $data['locale'];
    $scraper->recordNotFoundAsinMain($id, $table, $asin, $locale);
    break;

  case 'record-data':
    $data = $_GET;
    $scraper->recordDataMain($data, $data['table']);
    break;

  case 'reset':
    $scraper->resetMain();
    break;
}

?>
