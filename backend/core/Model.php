<?php

/*
|--------------------------------------------------------------------------
| Core : Base Model
|--------------------------------------------------------------------------
|
| - declare function for database logic
| - set table name and primary key
| - stay connect to the database
|
*/

/* # backend/core/Database */
require_once (__DIR__ .'/Database.php');

class Model 
{
  /*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $table = table's of databae
  | - $primaryKey = pk of the table
  | - $con = connection remains form database
  | - $statement = pdo prepare statement for sql
  | - $data = data of the model
  |
  */
  public $table;
  public $primaryKey;
  public $con;
  public $statement;
  public $data;

  /*
  |--------------------------------------------------------------------------
  | all
  |--------------------------------------------------------------------------
  |
  | fetch all the data from data source
  | and set to $data properties
  |
  */

  public function all()
  {
    $this->con = Database::connect();
    $this->statement = $this->con->prepare(
      " SELECT * FROM $this->table ");
    $this->statement->execute();
    $this->data = $this->statement->fetchAll();
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | find @params (string, int) $id
  |--------------------------------------------------------------------------
  |
  | fetch specific data from data source
  | @return $this
  |
  */

  public function find($id)
  {
    $this->con = Database::connect();
    $this->statement = $this->con->prepare(
      " SELECT * FROM $this->table WHERE $this->primaryKey = :id ");
    $this->statement->bindParam(':id', $id);
    $this->statement->execute();
    $this->data = $this->statement->fetch();
    $this->con = null;

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | where @params (string) $field | (string) $operator | (string, int) $value 
  |--------------------------------------------------------------------------
  |
  | select data with condition where
  | chain this method with first or get
  | @return $this
  |
  */

  public function where($field, $operator, $value)
  {
    $this->con = Database::connect();
    $field = $this->strEscape($field);
    $this->statement = $this->con->prepare(
      " SELECT * FROM $this->table WHERE $field $operator :value ");
    $this->statement->bindParam(':value', $value);

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | save @params (array) $data
  |--------------------------------------------------------------------------
  |
  | create data in database from input array
  |
  */

  public function save($data)
  {
    $this->con = Database::connect();
    $this->data = (object) $data;
    $strColumn = $this->getColumnInsert($data);
    $strColumnParams = $this->getColumnParamInsert($data);

    $this->statement = $this->con->prepare(
      " INSERT INTO $this->table($strColumn) VALUES($strColumnParams) ");
    $this->statement->execute((array) $data);
    $this->data->id = $this->con->lastInsertId();
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | update @params (array) $data
  |--------------------------------------------------------------------------
  |
  | update data in database from input array
  |
  */

  public function update($data, $id = null)
  {
    $this->con = Database::connect();
    if ($id != null) {
      $data = (array) $data;
      unset($data[$this->primaryKey]);
      $data[$this->primaryKey] = $id;
    }
    $this->data = (object) $data;
    $data = (array) $data;
    $id = $data[$this->primaryKey];
    unset($data[$this->primaryKey]);
    $strColumnParams = $this->getColumnParamUpdate($data);
    $data[$this->primaryKey] = $id;

    $this->statement = $this->con->prepare(
      " UPDATE $this->table SET $strColumnParams WHERE $this->primaryKey = :id ");
    $this->statement->execute($data);
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | delete @params (string, int) $id
  |--------------------------------------------------------------------------
  |
  | delete data from the database
  |
  */

  public function delete($id)
  {
    $this->con = Database::connect();
    $this->statement = $this->con->prepare(
      " DELETE FROM $this->table WHERE $this->primaryKey = :id ");
    $this->statement->bindParam(':id', $id);
    $this->statement->execute();
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | get
  |--------------------------------------------------------------------------
  |
  | chained method that execute the statement 
  | and fetch data as array
  |
  */

  public function get()
  {
    $this->statement->execute();
    $this->data = $this->statement->fetchAll();
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | first
  |--------------------------------------------------------------------------
  |
  | chained method that execute the statement 
  | and fetch data as a single object
  |
  */

  public function first()
  {
    $this->statement->execute();
    $this->data = $this->statement->fetch();
    $this->con = null;
  }

  /*
  |--------------------------------------------------------------------------
  | getColumnParamUpdate @params (array) $data
  |--------------------------------------------------------------------------
  |
  | get string for input to pdo statement
  | for update data to database
  | @return (string) $strColumn
  |
  */

  public function getColumnParamUpdate($data)
  {
    $strColumn = "";
    $i = 0;
    foreach ($data as $index => $item) {
      if ($i != 0) {
        $strColumn .= ",";
      }
      $strColumn .= $index;
      $strColumn .= "=";
      $strColumn .= ":";
      $strColumn .= $index;
      $i++;
    }

    return $strColumn;
  }

  /*
  |--------------------------------------------------------------------------
  | getColumnInsert @params (array) $data
  |--------------------------------------------------------------------------
  |
  | get string for input to pdo statement
  | for insert data to database
  | @return (string) $strColumn
  |
  */

  public function getColumnInsert($data)
  {
    $strColumn = "";
    $i = 0;
    foreach ($data as $index => $item) {
      if ($i != 0) {
        $strColumn .= ",";
      }
      $strColumn .= $index;

      $i++;
    }

    return $strColumn;
  }

  /*
  |--------------------------------------------------------------------------
  | getColumnParamInsert @params (array) $data
  |--------------------------------------------------------------------------
  |
  | get string param for input to pdo statement
  | for update data to database
  | @return (string) $strColumnParams
  |
  */

  public function getColumnParamInsert($data)
  {
    $strColumnParams = "";
    $i = 0;
    foreach ($data as $index => $item) {
      if ($i != 0) {
        $strColumnParams .= ",";
      }
      $strColumnParams .= ":";
      $strColumnParams .= $index;

      $i++;
    }

    return $strColumnParams;
  }

  /*
  |--------------------------------------------------------------------------
  | strEscape @params (string) $rawStr
  |--------------------------------------------------------------------------
  |
  | escape string for reserve word
  | prevent sql injection in pdo
  | @return (string) $rawStr
  |
  */

  public function strEscape($rawStr)
  {
    $rawStr = str_replace( array(";", ','), "", $rawStr);
    $rawStr = str_replace(
      array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), 
      array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), 
      $rawStr); 

    return $rawStr;
  }
}