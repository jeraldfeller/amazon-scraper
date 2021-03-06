<?php
set_time_limit(0);
//require '../Model/Init.php';
require '/var/www/html/am/amazon-scraper/Model/Init.php';
require ROOT . '/Model/Scraper.php';
require ROOT . '/Model/simple_html_dom.php';

$scraper = new Scraper();
$_GET['action'] = 'asin';
if(isset($_GET['action'])){
    $action = $_GET['action'];
    $limit = 5;
    $table = ($action == 'asin' ? 'asin_tbl' : 'asin_link_tbl');
    $data = json_decode($scraper->getAsin(), true);
   
//$data = array();

    $rowCount = 0;
    $context = stream_context_create(array(
        'http' => array(
            'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'),
        ),
   ));
//var_dump($data);die();
    foreach($data as $row){
        $id = $row['id'];
        if($action == 'asin'){
            $asin = $row['asin'];
           //$asin = 'B07BTMW3TS';
            $locale = strtolower($row['locale']);
	   // $locale = 'it';
            // Start Scraper
            // start fetch product link
            if($locale == 'uk'){
                $domExt = 'co.';
            }else{
                $domExt = '';
            }
            //$url = 'https://www.amazon.'.$domExt.$locale.'/s/field-keywords='.$asin.'';
            $link = 'https://www.amazon.'.$domExt.$locale.'/dp/'.$asin.'?th=1&psc=1';
            try{
                sleep(15);
                //  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim(file_get_contents($link, false, $context))));
                //$htmlData = file_get_contents($link, false, $context);
                $htmlData = $scraper->curlProxy($link, $locale);
				//print_r($htmlData);die('once');
		$ip = $htmlData['ip'];
//             var_dump($htmlData);
                if($htmlData){
//echo $htmlData['html'];
                //  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim($htmlData['html'])));

                  //$myfile = fopen(ROOT . '/tmp/tmp.php', "w") or die("Unable to open file!");
                  //fwrite($myfile, $htmlNew);
                  //fclose($myfile);

                  //$htmlNew = file_get_contents(ROOT  . '/tmp/tmp.php');
                  //$html = str_get_html($htmlNew);
                  $html = str_get_html($htmlData['html']);
                  $myfile = fopen(ROOT . '/tmp/test.html', "w") or die("Unable to open file!");
                    fwrite($myfile, $htmlData['html']);
                    fclose($myfile); 
                 if($html != false){
                      $price = $html->find('#priceblock_ourprice', 0);
 if(!$price){
                          $price = $html->find('#priceblock_dealprice', 0);
                               if(!$price){
                              $price = $html->find('.offer-price', 0);

                          }
                      }
			  $priceSellPrice = $html->find('#priceblock_saleprice', 0);
    $availability = $html->find('#availability_feature_div', 0);
                      $regionalAvailability = $html->find('#regionalAvailability_feature_div', 0);
                      $shippingFee = $html->find('.shipping3P', 0);
                      $deliveryMessage = $html->find('#ddmDeliveryMessage', 0);
                      $shippingMessage = $html->find('#price-shipping-message', 0);
                      $bbSeller = $html->find('#merchant-info', 0);
//var_dump($bbSeller->innerText());
                      if($bbSeller){

			$bbSellerLink = $bbSeller->find('a', 0);
}else{
$bbSellerLink = false;
}
$isPantry = $html->find('#pantryBadge');
  $salesRank = $html->find('#SalesRank', 0);
//if(!$salesRank){
  //    $salesRank = $html->find('.pdTab', 0);
 // }
                      if($salesRank){
if(!$isPantry){
                          $salesRankValue = $salesRank->find('.value', 0);
                          if(!$salesRankValue){
                                $salesRankValue = $salesRank;  
                          }
                       var_dump($salesRankValue->innerText()); 
                          $children = $salesRankValue->children; // get an array of children
                          foreach ($children AS $child) {
                              $child->outertext = ''; // This removes the element, but MAY NOT remove it from the original $myDiv
                          }
                          $salesRankValue = $salesRankValue->innertext;
                          $rankNo = preg_replace("/[^0-9]/", '', $salesRankValue);
                        }else{
                        	$salesRankValue = $salesRank->find('.value', 0);
                          $rankNo = $salesRankValue->find('.zg_hrsr_rank', 0)->plaintext;
                          $rankNo = preg_replace("/[^0-9]/", '', $rankNo);
                          $salesRankValue = $salesRankValue->find('.zg_hrsr_ladder', 0)->plaintext;

                        }
$rankText = str_replace($rankNo, '', $salesRankValue);
			$rankText = preg_replace('/[0-9]+/', '', $rankText);
if($locale == 'fr'){
                          $rankText = str_replace('en ', '', $rankText);
                        }
                        if($locale == 'uk'){
                          $rankText = str_replace(',, ', '', $rankText);
                          $rankText = str_replace(', ', '', $rankText);
                        }
                        if($locale == 'es'){
                            $rankText = str_replace('nº en ', '', $rankText);
                        }
$rankText = str_replace('n.', '', $rankText);
                        $rankText = str_replace('Nr.', '', $rankText);
                        $rankText = str_replace('in ', '', $rankText);
			$rankText = str_replace('.', '', $rankText);
	                $rankText = trim(str_replace('()', '', $rankText));
                        //echo $rankNo;
                       //echo $salesRankValue->innertext;
                      }else{
		$rankNo = '-';
		$rankText = '-';
}
echo 'START Rank';
echo $rankNo.'|';
echo $rankText;

                      $description = $html->find('#productDescription', 0);
                      $aPlus = $html->find('#aplus', 0);

if($price){
           //               $price = trim($html->find('#priceblock_ourprice', 0)->plaintext);
if(strpos($price, '£') > 0){
                              $currency = '£';
                          }elseif (strpos($price, 'EUR') > 0){
                              $currency = 'EUR';
                          }else{
                              $currency =  preg_replace('/[0-9,.]+/', '', $price);
                          }                          
$price = preg_replace("/[^0-9,.]/", "", $price);

                      }else{
                        if($priceSellPrice){
                          $price = trim($priceSellPrice->plaintext);
                          $currency =  preg_replace('/[0-9,.]+/', '', $price);
                          $price = preg_replace("/[^0-9,.]/", "", $price);
                        }else{
                          $price = 0;
                          $currency = '-';
                        }

                      }
                      if($availability){
                          $availability = trim($html->find('#availability_feature_div', 0)->plaintext);
                      }else{
                        if($regionalAvailability){
                          $availability = trim($regionalAvailability->plaintext);
                        }else{
                          $availability = '-';
                        }

                      }

                      if($shippingFee){
                          $shippingFee = trim($shippingFee->plaintext);
                      }else{
                          $shippingFee = '-';
                      }

                      if($deliveryMessage){
                          $deliveryMessage = trim($deliveryMessage->plaintext);
                          switch ($locale){
                    case 'it':
                        $deliveryMessage = str_replace('&nbsp;', ' ', $deliveryMessage);
                        break;
                    case 'de':
                        $deliveryMessage = str_replace('Siehe Details.', '', $deliveryMessage);
                        break;
                    case 'uk':
                        $deliveryMessage = str_replace('Details', '', $deliveryMessage);
                        break;
                }                     
 }else{
                          $deliveryMessage = '-';
                      }

                      if($shippingMessage){
                          $shippingMessage = trim($shippingMessage->plaintext);
                      }else{
                          $shippingMessage = '-';
                      }

                      if($bbSeller){
                          $bbSeller = trim($bbSeller->plaintext);
                         switch ($locale){
                              case 'uk':
                                  $bbSeller = str_replace('Gift-wrap available.', '', $bbSeller);
                                  break;
                              case 'it':
                                  $bbSeller = str_replace('Confezione regalo disponibile.', '', $bbSeller);
                                  break;
                              case 'de':
                                  $bbSeller = str_replace('Geschenkverpackung verf&uuml;gbar.', '', $bbSeller);
                                  break;
                              case 'fr':
                                  $bbSeller = str_replace('Emballage cadeau disponible.', '', $bbSeller);
                                  break;
                          }
                      }else{
if($isPantry){
                            if($locale == 'fr'){
                              $bbSeller = 'Expédié et vendu par Amazon';
                            }else if($locale == 'uk'){
                              $bbSeller = 'Dispatched from and sold by Amazon.';
                            }else if($locale == 'de'){
                              $bbSeller = 'Verkauf und Versand durch Amazon.';
                            }else if($locale == 'it'){
                              $bbSeller = 'Venduto e spedito da Amazon.';
                            }else if($locale == 'es'){
                              $bbSeller = 'Vendido y enviado por Amazon.';
                            }

                          }else{
                            $bbSeller = '-';
                          }

}

                      if($bbSellerLink){
                          if($bbSellerLink->find('a', 0) != null){
                            $bbSellerLink = $bbSellerLink->find('a', 0)->getAttribute('href');

                          }else{
                              $bbSellerLink = '-';
                          }

                      }else{
                          $bbSellerLink = '-';
                      }

                      if($description){
                          $description = trim($description->plaintext);
                      }else{
                          $description = '-';
                      }

                      if($aPlus){
                          $aPlusDescription = trim($aPlus->plaintext);
                          $aPlus = true;
                      }else{
                          $aPlusDescription = '-';
                          $aPlus = 0;
                      }

                      echo 'Link: ' . $link . '<br>';
                      echo 'Price: ' . $price . '<br>';
                      echo 'Currency: ' . $currency . '<br>';
                      echo 'Availability: '. $availability . '<br>';
                     echo 'Shipping: '. $shippingFee . '<br>';
                      echo 'Shipping Message: ' . $shippingMessage . '<br>';

                      echo 'Delivery Message: ' . $deliveryMessage . '<br>';
                      echo 'BB Seller: ' . $bbSeller . '<br>';
                      echo 'BB Link: ' . $bbSellerLink . '<br>';

                      echo 'Description: ' . $description . '<br>';
                      //echo 'Is A Plus: ' . $aPlus . '<br>';
                      //echo 'a Plus Description: ' . $aPlusDescription . '<br>';
                      //echo '<hr>';
           echo $asin;
                  if($price == '.'){
                          $price = 0;
                      }
                   if($price == 0){
                          $currency = '-';
                      }
			if($bbSeller == '-' && $bbSellerLink == '-' && $price == 0 && $availability == '-' && $currency == '-' && $deliveryMessage == '-' && $shippingFee == '-' && $shippingMessage == '-' && $availability == '-'){
                        $scraper->recordNotFoundAsin($id, $table, $asin, $locale);
                        $scraper->updateAsin(array(
                      'id' => $id,
                      'table' => $table,
                      'success' => 0,
                      'failed_message' => 'item not found'
                  ));
echo 'fail';
                      }else{
echo 'pass';
$data = array(
                          'id' => $id,
                          'asin' => $asin,
                          'locale' => $locale,
                          'bb_seller' => addslashes($bbSeller),
                          'bb_seller_link' => addslashes($bbSellerLink),
                          'price' => addslashes($price),
                          'currency' => addslashes($currency),
                          'delivery_message' => addslashes($deliveryMessage),
                          'shipping_price' => addslashes($shippingFee),
                          'shipping_message' => addslashes($shippingMessage),
                          'availability' => addslashes($availability),
                          'description' => addslashes($description),
                          'is_aplus' => addslashes($aPlus),
                          'aplus_description' => addslashes($aPlusDescription),
	                   'rank_no' => addslashes($rankNo),
                          'rank_text' => addslashes($rankText),
                           'ip' => addslashes($ip)
                      );

var_dump($data);
                      $scraper->recordData($data, $table);
                      }
                  }else{
                      $scraper->updateAsin(array(
                          'id' => $id,
                          'table' => $table,
                          'success' => 0,
                          'failed_message' => 'invalid html file'
                      ));
                  }

                }else{

                  echo 'Item not found: ' . $link . '<br>';
                  $scraper->updateAsin(array(
                      'id' => $id,
                      'table' => $table,
                      'success' => 0,
                      'failed_message' => 'item not found'
                  ));

                  $scraper->recordNotFoundAsin($asin, $locale);

                }


      }catch (ErrorException $e){
            echo $e;
        }
    }else if($action == 'link'){
        $asin = '';
        $locale = '';
        $link = $row['url'];
        $urlData = explode('/', $link);
        $domainData = explode('.', $urlData[2]);
        $asin = end($urlData);
        $locale = end($domainData);
        sleep(20);
        $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim(file_get_contents($link, false, $context))));
        $myfile = fopen(ROOT . '/tmp/tmp-link.php', "w") or die("Unable to open file!");
        fwrite($myfile, $htmlNew);
        fclose($myfile);

        $htmlNew = file_get_contents(ROOT  . '/tmp/tmp-link.php');
        $html = str_get_html($htmlNew);

        if($html != false){
            $price = $html->find('#priceblock_ourprice', 0);
            $availability = $html->find('#availability_feature_div', 0);
            $shippingFee = $html->find('.shipping3P', 0);
            $deliveryMessage = $html->find('#ddmDeliveryMessage', 0);
            $shippingMessage = $html->find('#price-shipping-message', 0);
            $bbSeller = $html->find('#merchant-info', 0);
            $bbSellerLink = $html->find('#olp_feature_div', 0);

            $description = $html->find('#productDescription', 0);
            $aPlus = $html->find('#aplus', 0);

            if($price){
                $price = trim($html->find('#priceblock_ourprice', 0)->plaintext);
                $currency =  preg_replace('/[0-9,.]+/', '', $price);
                $price = preg_replace("/[^0-9,.]/", "", $price);

            }else{
                $price = 0;
                $currency = '-';
            }

            if($availability){
                $availability = trim($html->find('#availability_feature_div', 0)->plaintext);
            }else{
                $availability = '-';
            }

            if($shippingFee){
                $shippingFee = trim($shippingFee->plaintext);
            }else{
                $shippingFee = '-';
            }

            if($deliveryMessage){
                $deliveryMessage = trim($deliveryMessage->plaintext);
             switch ($locale){
                    case 'it':
                        $deliveryMessage = str_replace('&nbsp;', ' ', $deliveryMessage);
                        break;
                    case 'de':
                        $deliveryMessage = str_replace('Siehe Details.', '', $deliveryMessage);
                        break;
                    case 'uk':
                        $deliveryMessage = str_replace('Details', '', $deliveryMessage);
                        break;
                }
            }else{
                $deliveryMessage = '-';
            }

            if($shippingMessage){
                $shippingMessage = trim($shippingMessage->plaintext);
            }else{
                $shippingMessage = '-';
            }

            if($bbSeller){
                $bbSeller = trim($bbSeller->plaintext);

            }else{
                $bbSeller = '';
            }

            if($bbSellerLink){
                $bbSellerLink = $bbSellerLink->find('a', 0)->getAttribute('href');
            }else{
                $bbSellerLink = '-';
            }

            if($description){
                $description = trim($description->plaintext);
            }else{
                $description = '-';
            }

            if($aPlus){
                $aPlusDescription = trim($aPlus->plaintext);
                $aPlus = true;
            }else{
                $aPlusDescription = '-';
                $aPlus = 0;
            }

            echo 'Link: ' . $link . '<br>';
            echo 'Price: ' . $price . '<br>';
            echo 'Currency: ' . $currency . '<br>';
            echo 'Availability: '. $availability . '<br>';
            echo 'Shipping: '. $shippingFee . '<br>';
            echo 'Shipping Message: ' . $shippingMessage . '<br>';

            echo 'Delivery Message: ' . $deliveryMessage . '<br>';
            echo 'BB Seller: ' . $bbSeller . '<br>';
            echo 'BB Link: ' . $bbSellerLink . '<br>';

            echo 'Description: ' . $description . '<br>';
            echo 'Is A Plus: ' . $aPlus . '<br>';
            echo 'a Plus Description: ' . $aPlusDescription . '<br>';
            echo '<hr>';

            $data = array(
                'id' => $id,
                'asin' => $asin,
                'locale' => $locale,
                'bb_seller' => $bbSeller,
                'bb_seller_link' => $bbSellerLink,
                'price' => $price,
                'currency' => $currency,
                'delivery_message' => $deliveryMessage,
                'shipping_price' => $shippingFee,
                'shipping_message' => $shippingMessage,
                'availability' => $availability,
                'description' => $description,
                'is_aplus' => $aPlus,
                'aplus_description' => $aPlusDescription
            );

            $scraper->recordData($data, $table);
        }else{
            $scraper->updateAsin(array(
                'id' => $id,
                'table' => $table,
                'success' => 0,
                'failed_message' => 'invalid html file'
            ));
        }
    }
}


}

?>
