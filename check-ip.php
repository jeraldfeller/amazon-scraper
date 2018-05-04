<?php
$type = $_GET['action'];
$ip = (isset($_GET['ip']) ? $_GET['ip'] : '');
$asin = $_GET['asin'];
$locale = $_GET['locale'];
if($locale == 'uk'){
    $domExt = 'co.';
}else{
    $domExt = '';
}
//$url = 'https://www.amazon.'.$domExt.$locale.'/s/field-keywords='.$asin.'';
$url = 'https://www.amazon.'.$domExt.$locale.'/dp/'.$asin.'';
echo $url;
if($action == 'luminati'){
  echo curlProxy($url, $locale)['html'];
}else{
  echo curlProxyInstantProxy($url, $locale)['html'];
}


function curlProxy($url, $locale){
    if($locale == 'es'){
      $username = 'lum-customer-hl_ea0ddc1c-zone-amazon_es';
      $password = 'g8nay06hflak';
      $port = 22225;
      $ip = 'es';
    }else if($locale == 'fr'){
      $username = 'lum-customer-hl_ea0ddc1c-zone-amazon_fr';
      $password = 'ie58zfcu3nwq';
      $port = 22225;
    }else if($locale == 'de'){
      $username = 'lum-customer-hl_ea0ddc1c-zone-amazon_de';
      $password = '4moz3kct1t9i';
      $port = 22225;
    }else if($locale == 'uk'){
      $username = 'lum-customer-hl_ea0ddc1c-zone-amazon_uk';
      $password = 'zk9ernpu2nhu';
      $port = 22225;
    }
    else{
      $username = 'lum-customer-hl_ea0ddc1c-zone-amazon_it';
      $password = 'jvvhns7shpyg';
      $port = 22225;
      $ip = 'it';
    }

    $user_agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
    $session = mt_rand();
    $super_proxy = 'zproxy.lum-superproxy.io';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
    curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username-remote-local-session-$session:$password");
     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
$result = curl_exec($curl);
    curl_close($curl);
    return array('html' => $result, 'ip' => $it);
  }


function curlProxyInstantProxy($url, $ip){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($proxy != NULL) {
        curl_setopt($curl, CURLOPT_PROXY, $ip);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    $contents = curl_exec($curl);
    curl_close($curl);
    return array('html' => $contents, 'ip' => $proxy);
}

?>
