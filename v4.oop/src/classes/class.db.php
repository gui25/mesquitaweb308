<?php

if (!isset($magic) or !defined($magic)){
  http_response_code(404); 
  die("404 Not Found"); 
}; 

// Singleton to connect db.
abstract class DbSingleton {
    protected static $host = DB_HOST;
    protected static $user = DB_USER;
    protected static $pass = DB_PASS;
    protected static $dbname = DB_NAME;
    protected static $conn;
    protected static $instance = array();

    protected static function connect() {
        
    }
    
    private function __construct()
    {
      $this->conn = $this->connect();
    }

    public static function getInstance()
    {
      trace( "<p> getInstance: ".get_called_class() ) ; 
      if(!isset(self::$instance[get_called_class()]))
      {
        trace(" criou nova instancia");
        self::$instance[get_called_class()] = static::connect();
      }  
      
      return self::$instance[get_called_class()];
    }
    
    public function getConnection()
    {
      return $this->conn;
    }
}

class DBPDO extends DbSingleton {
    protected static function connect() {
      try {  
        return new PDO("mysql:host=". static::$host . ";dbname=" . static::$dbname, 
          static::$user, static::$pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
      } catch (Exception $e) {
        echo "<p>erro criando pdo";
        var_dump($e);
        return null;  
      }  
    }
}

class DBMySQL extends DbSingleton {
      protected static function connect() {
        try {  
            return new mysqli(static::$host, static::$user, static::$pass, static::$dbname);
       
        } catch (Exception $e) {
            return null;  
        } 
    }
}




?>