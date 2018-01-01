<?php 

/*
|--------------------------------------------------------------------------
| Config : Data
|--------------------------------------------------------------------------
|
| define functions for controller to inherits
| render function require a view with pass data
| json function return a json with header set json
|
*/

/* # backend/config/database */
require_once (__DIR__ .'/../config/database.php');

class Database {

  /*
  |--------------------------------------------------------------------------
  | connect
  |--------------------------------------------------------------------------
  |
  | - define host, name, user, password for database connection
  | - config PDO Attribute (fetch data as object, error mode exception, emulate auto pdo to false)
  | - set charset to utf-8
  | @return PDO $con
  |
  */

  public function connect()
  {
    try {
      $db_host = DbConfig::$host_url;
      $db_name = DbConfig::$database_name;
      $db_user = DbConfig::$database_user;
      $user_pw = DbConfig::$password;
    
      $con = new PDO('mysql:host='.$db_host.'; dbname='.$db_name, $db_user, $user_pw);  
      $con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      $con->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
      $con->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
      $con->exec("SET CHARACTER SET utf8");

      return $con;
    }
    catch (PDOException $err) {  
      $err->getMessage() . "<br/>";
      file_put_contents('PDOErrors.txt',$err, FILE_APPEND);
      die( $err->getMessage());
    }
  }
}