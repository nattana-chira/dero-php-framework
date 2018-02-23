<?php 

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Database
|--------------------------------------------------------------------------
|
| define a function to connect to database
| use PDO statement to execute all the database command
|
*/

use Config\DbConfig;
use PDO;

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

  public static function connect()
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
      file_put_contents(__DIR__.'/log/PDOErrors.txt', $err.PHP_EOL.PHP_EOL, FILE_APPEND);
      die( $err->getMessage());
    }
  }

   /*
  |--------------------------------------------------------------------------
  | raw @params (string) $rawSql | (array) $bindedValue
  |--------------------------------------------------------------------------
  |
  | execute the raw sql command and get the result as an array
  | bind params (if exists) to prevent sql injection
  | set return data from db to property data
  |
  */

  public function raw($rawSql, $bindedValue = array())
  {
    $con = self::connect();
    $statement = $con->prepare($rawSql);
    $statement->execute($bindedValue);
    $con = null;

    return $statement->fetchAll();
  }
}