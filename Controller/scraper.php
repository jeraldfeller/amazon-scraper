<?php
//require '../Model/Init.php';
require '/var/www/html/am/Model/Init.php';
require ROOT . '/Model/Scraper.php';
require ROOT . '/Model/simple_html_dom.php';
$scraper = new Scraper();

switch ($_GET['action']) {
  case 'import':

  if (isset($_FILES['importFile']['tmp_name']) && $_POST['importType'] != 0) {
          if (pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION) == 'csv') {
              $file = $_FILES['importFile']['tmp_name'];
              $fileName = $_FILES['importFile']['name'];
              $flag = true;
              $fileHandle = fopen($_FILES['importFile']['tmp_name'], "r");
              while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
                  if ($flag) {
                      $flag = false;
                      continue;
                  }

                  if($_POST['importType'] == 1){
                    if(count($data) > 1){
                      $asin = trim($data[0]);
                      $locale = trim($data[1]);

                      $setData = array(
                        'asin' => $asin,
                        'locale' => $locale
                      );

                      $scraper->recordAsin($setData);
                    }
                  }else if($_POST['importType'] == 2){
                     $url = $data[0];
                     if(strpos($url, 'amazon')){
                       $scraper->recordAsinLink($url);
                     }

                  }

              }

              fclose($fileHandle);
          }




        echo true;
      }else{
        echo false;
      }
    break;
   case 'remove':
  if (isset($_FILES['importFile']['tmp_name']) && $_POST['importType'] != 0) {
          if (pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION) == 'csv') {
              $file = $_FILES['importFile']['tmp_name'];
              $fileName = $_FILES['importFile']['name'];
              $flag = true;
              $fileHandle = fopen($_FILES['importFile']['tmp_name'], "r");
              while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
                  if ($flag) {
                      $flag = false;
                      continue;
                  }

                     $asin = $data[0];
                     $scraper->deleteAsin($asin);


              }

              fclose($fileHandle);
          }




        echo true;
      }else{
        echo false;
      }
    break;
  default:
    # code...
    break;
}

?>
