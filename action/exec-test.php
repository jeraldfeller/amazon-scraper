<?php
set_time_limit(0);
//require '../Model/Init.php';
require '/var/www/html/am/Model/Init.php';
require ROOT . '/Model/Scraper.php';
require ROOT . '/Model/simple_html_dom.php';

$scraper = new Scraper();
$_GET['action'] = 'asin';
if(isset($_GET['action'])){
    $action = $_GET['action'];
    $limit = 5;
    $table = ($action == 'asin' ? 'asin_tbl' : 'asin_link_tbl');
    $data = $scraper->getAsinTest('B00F1HA0UM', 'it');
    $rowCount = 0;
    $context = stream_context_create(array(
        'http' => array(
            'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'),
        ),
    ));
    foreach($data as $row){
        $id = $row['id'];
        if($action == 'asin'){
            $asin = $row['asin'];
            $locale = strtolower($row['locale']);
            // Start Scraper
            // start fetch product link
            if($locale == 'uk'){
                $domExt = 'co.';
            }else{
                $domExt = '';
            }
            //$url = 'https://www.amazon.'.$domExt.$locale.'/s/field-keywords='.$asin.'';
            $link = 'https://www.amazon.'.$domExt.$locale.'/dp/'.$asin.'';
            try{
                sleep(20);
                //  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim(file_get_contents($link, false, $context))));
                //$htmlData = file_get_contents($link, false, $context);
                $htmlData = $scraper->curlProxy($link, $locale);
//                var_dump($htmlData);
                if($htmlData){
                  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim($htmlData)));

                  $myfile = fopen(ROOT . '/tmp/tmp.php', "w") or die("Unable to open file!");
                  fwrite($myfile, $htmlNew);
                  fclose($myfile);

                  $htmlNew = file_get_contents(ROOT  . '/tmp/tmp.php');
                  $html = str_get_html($htmlNew);

                  if($html != false){
                      $price = $html->find('#priceblock_ourprice', 0);
                      $availability = $html->find('#availability_feature_div', 0);
                      $regionalAvailability = $html->find('#regionalAvailability_feature_div', 0);
                      $shippingFee = $html->find('.shipping3P', 0);
                      $deliveryMessage = $html->find('#ddmDeliveryMessage', 0);
                      $shippingMessage = $html->find('#price-shipping-message', 0);
                      $bbSeller = $html->find('#shipsFromSoldBy_feature_div', 0);
                      $bbSellerLink = $bbSeller->find('a', 0);


                      $description = $html->find('#productDescription', 0);
                      $aPlus = $html->find('#aplus', 0);


//$childr//en = $bbSeller->children; // get an array of children
           //           foreach ($children as $child) {
  //                        $child->outertext = ''; // This removes the element, but MAY NOT remove it from the original $myDiv
       //               }
    ///                  echo $bbSeller->innertext;
//echo $bbSeller->plaintext;
//echo $bbSeller->innertext;
//echo $bbSeller->outertext;
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
                          $bbSeller = '-';
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

                //      $scraper->recordData($data, $table);
                  }else{
                    /*
                      $scraper->updateAsin(array(
                          'id' => $id,
                          'table' => $table,
                          'success' => 0,
                          'failed_message' => 'invalid html file'
                      ));
                      */
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

