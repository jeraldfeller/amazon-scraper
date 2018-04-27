<?php

class API{
  public $debug = TRUE;
  protected $db_pdo;

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


?>
