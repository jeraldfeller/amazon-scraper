<?php
set_time_limit(0);
require '../Model/Init.php';
//require '/var/www/html/am/amazon-scraper/Model/Init.php';
require ROOT . '/Model/Scraper.php';
require ROOT . '/Model/simple_html_dom.php';

$scraper = new Scraper();

$list = $scraper->getProductTbl();
$date = date('Y-m-d_H-i-s');
//var_dump($list);
$csv = 'product_tbl_'.$date.'.csv';
$data[] = implode('","', array(
    'ASIN',
    'LOCALE',
    'BB SELLER',
    'BB_SELLER_LINK',
    'PRICE',
    'CURRENCY',
    'DELIVERY MESSAGE',
    'SHIPPING PRICE',
    'SHIPPING MESSAGE',
    'AVAILABILITY',
    'DESCRIPTION',
    'IS APLUS',
    'APLUS DESCRIPTION',
	'RANK NO',
    'RANK TEXT',
   'IP',
    'TIMESTAMP'
));

foreach($list as $row){
  $data[] = implode('","', array(
	$row['asin'],
            $row['locale'],
            trim(preg_replace('/\s+/', ' ', html_entity_decode($row['bb_seller']))),
            $row['bb_seller_link'],
            $row['price'],
            $row['currency'],
            trim(preg_replace('/\s+/', ' ', html_entity_decode($row['delivery_message']))),
            str_replace('+', '', html_entity_decode($row['shipping_price'])),
            str_replace('+', '', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['shipping_message'])))),
            trim(preg_replace('/\s+/', ' ', html_entity_decode($row['availability']))),
            trim(preg_replace('/\s+/', ' ', html_entity_decode($row['description']))),
            $row['is_aplus'],
            trim(preg_replace('/\s+/', ' ', html_entity_decode($row['aplus_description']))),
$row['rank_no'],
          trim(preg_replace('/\s+/', ' ', html_entity_decode($row['rank_text']))),
$row['ip'],        
    $row['timestamp']
)
  );
}



$file = fopen($csv,"a");
foreach ($data as $line){
    fputcsv($file, explode('","',$line));
}
fclose($file);



// Output CSV-specific headers

header('Content-Type: text/csv; charset=utf-8');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($csv) . "\"");
readfile($csv);

?>

