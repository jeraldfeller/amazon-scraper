<?php

class Scraper
{
    public $debug = TRUE;
    protected $db_pdo;

    public function recordAsin($data){
      $pdo = $this->getPdo();
      $sql = 'INSERT INTO `asin_tbl`
              (`asin`, `locale`) VALUES ("'.$data['asin'].'", "'.$data['locale'].'")';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      return true;
    }

    public function getProductTbl(){
      $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `product_tbl`
                ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $content[] = $row;
        }

        return $content;
    }

    public function recordNotFoundAsin($id, $table, $asin, $locale){
	     $this->updateAsin(array(
            'id' => $id,
            'table' => $table,
            'success' => 1,
            'failed_message' => 'item not found'
        ));
      $param = http_build_query(array(
          'id' => $id,
          'asin' => $asin,
          'locale' => $locale
      ));
      $url = 'http://51.15.193.78/am/api/api.php?action=insert-rescan&'.$param;

      $this->curlTo($url);
      return true;
    }

    public function recordNotFoundAsinMain($id, $table, $asin, $locale){
	$this->updateAsin(array(
            'id' => $id,
            'table' => $table,
            'success' => 1,
            'failed_message' => 'item not found'
        ));
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `rescan_asin_tbl` WHERE `asin` = "' . $asin . '" AND `locale` = "' . $locale . '"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      if(!$stmt->fetch(PDO::FETCH_ASSOC)){

      $sql = 'INSERT INTO `rescan_asin_tbl`
              (`asin`, `locale`) VALUES ("'.$asin.'", "'.$locale.'")';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
}
      return true;
    }

    public function recordAsinLink($url){
      $pdo = $this->getPdo();
      $sql = 'INSERT INTO `asin_link_tbl`
              (`url`) VALUES ("'.$url.'")';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      return true;
    }

    public function getAsin(){
      $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://51.15.193.78/am/api/api.php?action=get-asin",
          CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return array();
        } else {
          return $response;
        }
    }

public function getAsinNew($table, $limit){
        $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `'.$table.'`
                WHERE `completed` = 0 ORDER BY `id` LIMIT 1
                ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[] = $row;
        }

foreach($content as $row){
          $this->updateAsin(array(
              'id' => $row['id'],
              'table' => $table,
              'success' => 0,
              'failed_message' => ''
          ));
        }
        return $content;
    }

    public function reset(){
      $url = 'http://51.15.193.78/am/api/api.php?action=reset';
      $this->curlTo($url);
      return true;
    }

    public function resetMain(){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `asin_tbl` SET `completed` = 0';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

	$sql = 'DELETE FROM `rescan_asin_tbl`';
$stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

	public function getAsinTest($asin, $locale){
        $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `asin_tbl` WHERE `asin` = "' . $asin . '" AND `locale` = "' . $locale . '"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[] = $row;
        }

        return $content;
    }

    public function getRescanAsin(){
      $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `rescan_asin_tbl`
                WHERE `completed` = 0 AND `success` = 0;
		ORDER BY `id` LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $content[] = $row;
        }

foreach($content as $row){
          $this->updateAsin(array(
              'id' => $row['id'],
              'table' => 'rescan_asin_tbl',
              'success' => 0,
              'failed_message' => ''
          ));
        }

        return $content;
    }

public function getRescanSuccessAsin(){
        $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `asin_tbl`
                WHERE `completed` = 1 AND `success` = 0 ORDER BY `id` LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[] = $row;
        }
        return $content;
    }

    public function recordData($data, $table){
      $data['table'] = $table;
      $param = http_build_query($data);
      $url = 'http://51.15.193.78/am/api/api.php?action=record-data';
	$return = $this->curlTo($url, $data);
var_dump($return);      
return $return;    
}


public function recordDataMain($data, $table){
        $pdo = $this->getPdo();
        if($table == 'rescan_asin_tbl'){
          $sql = 'UPDATE `product_tbl` SET `bb_seller` = "'. addslashes(htmlentities($data['bb_seller'])) .'",
          `bb_seller_link` = "'. addslashes($data['bb_seller_link']).'",
          `price` = "'. addslashes($data['price']).'",
          `currency` = "'. addslashes($data['currency']).'",
          `delivery_message` = "'. addslashes($data['delivery_message']).'",
          `shipping_price` = "'. addslashes($data['shipping_price']).'",
          `shipping_message` = "'. addslashes($data['shipping_message']).'",
          `availability` = "'. addslashes($data['availability']).'",
          `description` = "'. addslashes($data['description']).'",
          `is_aplus` = "'. addslashes($data['is_aplus']).'",
          `aplus_description` = "'. addslashes($data['aplus_description']).'",
`rank_no` = "'.$data['rank_no'].'",
          `rank_text` = "'.addslashes($data['rank_text']).'",
`ip` = "'.$data['id'].'"
          WHERE `id` = '.$data['id'].'i
          ';
        }else{
          $sql = 'INSERT INTO `product_tbl`
              (`asin`,
              `locale`,
              `bb_seller`,
              `bb_seller_link`,
              `price`,
              `currency`,
              `delivery_message`,
              `shipping_price`,
              `shipping_message`,
              `availability`,
              `description`,
              `is_aplus`,
              `aplus_description`,
			`rank_no`,
             `rank_text`,
`ip`)
              VALUES ("'.$data['asin'].'",
              "'.$data['locale'].'",
              "'. addslashes(htmlentities($data['bb_seller'])) .'",
              "'. addslashes($data['bb_seller_link']).'",
              "'. addslashes($data['price']).'",
              "'. addslashes($data['currency']).'",
              "'. addslashes($data['delivery_message']).'",
              "'. addslashes($data['shipping_price']).'",
              "'. addslashes($data['shipping_message']).'",
              "'. addslashes($data['availability']).'",
              "'. addslashes($data['description']).'",
              "'. addslashes($data['is_aplus']).'",
              "'. addslashes($data['aplus_description']).'",
"'.$data['rank_no'].'",
              "'.addslashes($data['rank_text']).'",
"' . $data['ip'] .'"
            )';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $this->updateAsin(array(
            'id' => $data['id'],
            'table' => $table,
            'success' => true,
            'failed_message' => ''
        ));

        if($table == 'rescan_asin_tbl'){
          $sql = 'DELETE FROM `rescan_asin_tbl` WHERE `id` = '.$data['id'] . '';
        }
        return true;
    }

    public function updateAsin($data){
      $param = http_build_query($data);
      $url = 'http://51.15.193.78/am/api/api.php?action=update-asin';
      $this->curlTo($url, $data);
      return true;
    }


    public function updateAsinMain($data){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `'.$data['table'].'` SET `completed` = 1, `success` = '.$data['success'].', `failed_message` = "' . $data['failed_message'] . '" WHERE `id` = '.$data['id'].'';

      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    }


    public function updateRescanAsin($id){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `rescan_asin_tbl` SET `completed` = 1 WHERE `id` = '.$id.'';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    }


    public function delete_all_between($beginning, $end, $string) {
      $beginningPos = strpos($string, $beginning);
      $endPos = strpos($string, $end);
      if ($beginningPos === false || $endPos === false) {
        return $string;
      }

      $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

      return str_replace($textToDelete, '', $string);
    }
public function deleteAsin($asin){
      $pdo = $this->getPdo();
      $sql = 'DELETE FROM `asin_tbl` WHERE `asin` = "'.$asin.'"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    }

public function curlProxy($url, $locale){
          $result = $this->getBestProxyList($url, $locale);
          return array('html' => $result['html'], 'ip' => $result['ip']);
}

public function getProxyList($url, $locale){
      switch($locale){
        case 'uk':
            $proxy = array(
              '77.75.126.214:8800',
              '77.75.126.144:8800',
              '31.132.1.191:8800',
              '31.132.1.245:8800',
              '94.46.184.249:8800',
              '94.46.184.80:8800',
              '81.92.194.178:8800',
              '81.92.194.161:8800',
              '81.92.194.151:8800',
              '81.92.194.204:8800'
            );
          break;
        case 'fr':
          $proxy = array(
            '185.167.68.22:8800',
            '185.167.68.207:8800',
            '185.167.68.88:8800',
            '185.167.68.60:8800',
            '185.167.68.46:8800',
            '185.167.69.163:8800',
            '185.167.69.148:8800',
            '185.167.69.135:8800',
            '185.167.69.229:8800',
            '185.167.69.111:8800'
          );
          break;
        case 'de':
        $proxy = array(
          '185.170.212.96:8800',
          '185.170.212.176:8800',
          '185.170.212.223:8800',
          '185.170.213.219:8800',
          '185.170.213.210:8800',
          '185.170.213.220:8800',
          '185.170.214.177:8800',
          '185.170.214.192:8800',
          '185.170.214.98:8800',
          '185.170.215.148:8800',
          '185.170.215.228:8800',
          '185.170.215.7:8800',
          '185.170.215.220:8800',
          '185.170.215.174:8800',
          '185.170.215.20:8800'
        );
          break;
      }


      $curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $url);
    	if ($proxy != NULL) {
    		curl_setopt($curl, CURLOPT_PROXY, $proxy[mt_rand(0, count($proxy) - 1)]);
    	}
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	$contents = curl_exec($curl);
    	curl_close($curl);
    	return $contents;

    }
   public function getProxy(){
$proxy = array('78.157.213.48:3128',
'81.92.195.18:3128',
'185.170.212.223:3128',
'185.170.215.7:3128',
'185.170.215.220:3128',
'185.170.212.176:3128',
'81.92.195.232:3128',
'185.170.212.96:3128',
'78.157.213.139:3128',
'185.170.215.228:3128',
'185.167.68.250:3128',
'179.43.132.139:3128',
'185.170.214.196:3128',
'185.170.214.220:3128',
'179.43.132.184:3128',
'185.170.215.240:3128',
'185.167.68.16:3128',
'185.170.213.6:3128',
'185.170.215.47:3128',
'185.167.68.138:3128',
'185.170.212.192:3128',
'185.170.214.233:3128',
'185.167.68.22:3128',
'179.43.132.25:3128',
'185.170.215.15:3128',
'185.167.68.207:3128',
'185.170.215.215:3128',
'185.170.215.192:3128',
'185.170.213.212:3128',
'185.170.215.250:3128',
'185.170.213.2:3128',
'185.170.212.36:3128',
'185.170.212.184:3128',
'179.43.132.132:3128',
'179.43.132.29:3128'
        );
return $proxy[mt_rand(0,34)];
    }

public function getBestProxyList($url, $locale){
      //IT
      switch($locale){
        case 'it':
          $port = 43848;
          $proxy = array(
            '213.184.109.174',
            '213.184.109.248',
            '213.184.110.103',
            '213.184.110.9',
            '213.184.112.84',
            '213.184.114.120'
          );
          break;
        case 'fr':
          $port = 56362;
          $proxy = array(
            '79.137.58.75',
            '79.137.58.84',
            '79.137.58.87',
            '79.137.58.88'
          );
          break;
        case 'de':
          $port = 56362;
          $proxy = array(
            '185.246.212.227',
            '185.246.213.219',
            '185.246.214.221',
            '185.246.215.224'
          );
          break;
        case 'uk':
          $port = 56362;
          $proxy = array(
            '78.157.195.45',
            '78.157.203.174',
            '5.101.144.109',
            '78.157.202.192'
          );
          break;
        case 'es':
        $port = 56362;
        $proxy = array(
          '79.137.58.75',
          '79.137.58.84',
          '79.137.58.87',
          '79.137.58.88',
          '185.246.212.227',
          '185.246.213.219',
          '185.246.214.221',
          '185.246.215.224',
          '78.157.195.45',
          '78.157.203.174',
          '5.101.144.109',
          '78.157.202.192'
        );
          break;
      }

      $ip = $proxy[mt_rand(0,count($proxy) - 1)];
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_TIMEOUT, 20);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      curl_setopt($curl, CURLOPT_PROXY, $ip);
      curl_setopt($curl, CURLOPT_PROXYPORT, $port);
      curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'amznscp:dfab7c358');
      curl_setopt($curl, CURLOPT_URL, $url);
      $content = curl_exec($curl);

      return array('html' => $content, 'ip' => $ip);
    }

    public function curlTo($url, $post){
      $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
		CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $post,
          CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return array();
        } else {
          return $response;
        }
    }
    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}
