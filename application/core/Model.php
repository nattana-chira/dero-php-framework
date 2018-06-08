<?php 

namespace Core;

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

use Core\Database;
use Core\CustomExceptionMessage;
use stdClass;

class Model 
{

  /*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | // RESERVED WORDS //
  | - $_table = table's of databae
  | - $_primaryKey = pk of the table
  | - $_con = connection remains form database
  | - $_statement = pdo prepare statement for sql
  | - $_data = data of the model
  | - $_queryBuilder = data for build sql statement
  | - $_uniqueValue = static random number for multiple parameter
  | - $_reservedWords = array of reserved words
  | 
  */

  private $_mainData = null;
  private $_data;
  private static $_uniqueValue = 0;
  private $_table;
  private $_primaryKey;
  private $_con;
  private $_statement;
  private $_lastInsertId;
  private $_queryBuilder = array(
    'select' => array(),
    'where' => array(),
    'join' => array(),
    'orderBy' => array(),
    'bindedValue' => array(),
    'with' => array(),
    'limit' => null,
    'offset' => null,
    'action' => 'select'
  );
  private $_reservedWords = array(
    '_mainData',
    '_data',
    '_uniqueValue',
    '_table',
    '_primaryKey',
    '_con',
    '_statement',
    '_queryBuilder',
    '_reservedWords',
    '_lastInsertId'
  );

  /*
  |--------------------------------------------------------------------------
  | __construct
  |--------------------------------------------------------------------------
  |
  | unset inheritanced model's properties
  |
  */

  function __construct(){
    $this->_table = $this->table;
    $this->_primaryKey = $this->primaryKey;

    unset($this->table);
    unset($this->primaryKey);
  }

  /*
  |--------------------------------------------------------------------------
  | data
  |--------------------------------------------------------------------------
  |
  | return data from the sql command result
  |
  */

  public function data()
  {
    return $this->_data;
  }

  /*
  |--------------------------------------------------------------------------
  | lastInsertId()
  |--------------------------------------------------------------------------
  |
  | return last id inserted in database
  |
  */

  public function lastInsertId()
  {
    return $this->_lastInsertId;
  }

  /*
  |--------------------------------------------------------------------------
  | with @params (string) $method
  |--------------------------------------------------------------------------
  |
  | get method name as parameter
  | execute the method 
  | need to define relation in model first
  |
  */

  public function with($method)
  {
    array_push($this->_queryBuilder['with'], $method);

    return $this;
  }

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
    $sql = " SELECT * FROM $this->_table ";
    return $this->execute($sql, null, 'fetchAll');
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
   
    $sql = " SELECT * FROM $this->_table WHERE $this->_primaryKey = ? ";
    return $this->execute($sql, array($id), 'fetch');
  }

  /*
  |--------------------------------------------------------------------------
  | select @params (string) one or more arguments required
  |--------------------------------------------------------------------------
  |
  | select only data you set
  | chain this method with first or get to execute action
  | @return $this
  |
  */

  public function select()
  {
    $arguments = func_get_args();

    foreach ($arguments as $index => $argument) {
      array_push($this->_queryBuilder['select'], $argument);
    }

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | where @params (string) $field | (string) $operator | (string, int) $value 
  |--------------------------------------------------------------------------
  |
  | select data with condition where
  | chain this method with first or get to execute action
  | @return $this
  |
  */

  public function where($field, $operator, $value)
  {
    $uniqueValue = self::$_uniqueValue;
    self::$_uniqueValue ++;

    $sql = " $field $operator :$uniqueValue "; 
    $this->_queryBuilder['bindedValue'][":$uniqueValue"] = $value;
    array_push($this->_queryBuilder['where'], $sql);

    return $this;
  }

  public function orderBy($field, $order = 'ASC')
  {
    $sql = " $field $order ";
    array_push($this->_queryBuilder['orderBy'], $sql);

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | limit @params (int) $limitValue
  |--------------------------------------------------------------------------
  |
  | limit the data you fetch from database
  | chain this method with get to execute action
  | @return $this
  |
  */

  public function limit($limitValue)
  {
    $this->_queryBuilder['limit'] = (int) $limitValue;

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | offset @params (int) $offsetValue
  |--------------------------------------------------------------------------
  |
  | offset (skip) the data you fetch from database
  | chain this method with get to execute action
  | @return $this
  |
  */

  public function offset($offsetValue)
  {
    $this->_queryBuilder['offset'] = (int) $offsetValue;

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | join @params (string) $subTable | (string) $mainKey | (string) $operator | (string) $subKey
  |--------------------------------------------------------------------------
  |
  | inner join the main model with another sub model
  | chain this method with first or get to execute action
  | @return $this
  |
  */

  public function join($subTable, $mainKey, $operator, $subKey)
  {
    $sql = " INNER JOIN $subTable ON $mainKey $operator $subKey ";
    array_push($this->_queryBuilder['join'], $sql);

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | leftJoin @params (string) $subTable | (string) $mainKey | (string) $operator | (string) $subKey
  |--------------------------------------------------------------------------
  |
  | left join the main model with another sub model
  | chain this method with first or get to execute action
  | @return $this
  |
  */

  public function leftJoin($subTable, $mainKey, $operator, $subKey)
  {
    $sql = " LEFT JOIN $subTable ON $mainKey $operator $subKey ";
    array_push($this->_queryBuilder['join'], $sql);

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | rightJoin @params (string) $subTable | (string) $mainKey | (string) $operator | (string) $subKey
  |--------------------------------------------------------------------------
  |
  | right join the main model with another sub model
  | chain this method with first or get to execute action
  | @return $this
  |
  */

  public function rightJoin($subTable, $mainKey, $operator, $subKey)
  {
    $sql = " RIGHT JOIN $subTable ON $mainKey $operator $subKey ";
    array_push($this->_queryBuilder['join'], $sql);

    return $this;
  }

  /*
  |--------------------------------------------------------------------------
  | save @params
  |--------------------------------------------------------------------------
  |
  | create data in database from attribute set to the model
  |
  */

  public function save()
  {
    $this->_queryBuilder['action'] = 'insert';
    $this->_data = new stdClass();
    foreach ($this as $name => $value) {
      if (! in_array($name, $this->_reservedWords)) 
        $this->_data->$name = $value;
    }

    $sql = $this->queryBuilder();
    $this->execute($sql);
  }

  /*
  |--------------------------------------------------------------------------
  | create @params (array) $data
  |--------------------------------------------------------------------------
  |
  | create data in database from input array
  |
  */

  public function create($data)
  {
    $this->_queryBuilder['action'] = 'insert';
    $this->_data = (object) $data;

    $sql = $this->queryBuilder();
    $this->execute($sql);
  }

  /*
  |--------------------------------------------------------------------------
  | update @params (array) $data
  |--------------------------------------------------------------------------
  |
  | update data in database from input array
  |
  */

  public function update($data = null)
  {
    $this->_queryBuilder['action'] = 'update';
    if ($data !== null) {
      $this->_data = (object) $data;
      $data = (array) $data;
    } 
    else {
      $this->_data = new stdClass();
      foreach ($this as $name => $value) {
        if (! in_array($name, $this->_reservedWords)) 
          $this->_data->$name = $value;
      }
      $data = (array) $this->_data;
    }

    $sql = $this->queryBuilder();
    $this->execute($sql);
  }

  /*
  |--------------------------------------------------------------------------
  | delete @params (string, int) $id
  |--------------------------------------------------------------------------
  |
  | delete data from the database
  |
  */

  public function delete()
  {
    $this->_queryBuilder['action'] = 'delete';
    $sql = $this->queryBuilder();
    $this->execute($sql);
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
    if ( count($this->_queryBuilder['with']) > 0) {
      foreach ($this->_queryBuilder['with'] as $key => $methodName) {
        // $methodName = $this->_queryBuilder['with'][0];
        $this->$methodName();

      }
      // $methodName = $this->_queryBuilder['with'][0];
      // $this->$methodName();
    } 
    else {
      $sql = $this->queryBuilder();
      $this->execute($sql, null, 'fetchAll');
    }

    return $this->_data;
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
    $this->_queryBuilder['limit'] = 1;
    if ( count($this->_queryBuilder['with']) > 0) {
      $methodName = $this->_queryBuilder['with'][0];
      $this->$methodName();

       return $this->_data[0];
    } 

    $sql = $this->queryBuilder();
    $this->execute($sql, null, 'fetch');

    return $this->_data;
    
  }

  /*
  |--------------------------------------------------------------------------
  | hasMany @params (object) $model | (string) $primaryKey
  |--------------------------------------------------------------------------
  |
  | pre define relation in model 
  | get sub model object and foreign key of sub model as arguments
  | set result data to property data as an array
  |
  */

  protected function hasMany($model, $primaryKey)
  {
    $table = $model->_table;
    $queryData = $this->queryWith($table, $primaryKey);
    $mainData = $queryData['main'];
    $subData = $queryData['sub'];

    foreach($mainData as $mainIndex => $mainObj) {
      $mainObj->$table = array();
      foreach($mainObj as $mainKey => $mainValue) {
        if ($mainKey == $this->_primaryKey) {
          foreach ($subData as $subIndex => $subObj) {
            foreach ($subObj as $subKey => $subValue) {
              if ($subKey == $primaryKey) {
                if ($mainValue == $subValue) {
                  array_push($mainObj->$table, $subObj);
                }
              }
            }
          }
        }
      }
    }

    $this->_data = $mainData;
  }

  /*
  |--------------------------------------------------------------------------
  | hasOne @params (object) $model | (string) $primaryKey
  |--------------------------------------------------------------------------
  |
  | pre define relation in model 
  | get sub model object and foreign key of sub model as arguments
  | set result data to property data as an object
  |
  */

  protected function hasOne($model, $primaryKey)
  {
    $table = $model->_table;
    $queryData = $this->queryWith($table, $primaryKey);
    $mainData = $queryData['main'];
    $subData = $queryData['sub'];

    foreach($mainData as $mainIndex => $mainObj) {
      $mainObj->$table = null;
      foreach($mainObj as $mainKey => $mainValue) {
        if ($mainKey == $this->_primaryKey) {
          foreach ($subData as $subIndex => $subObj) {
            foreach ($subObj as $subKey => $subValue) {
              if ($subKey == $primaryKey) {
                if ($mainValue == $subValue) {
                  $mainObj->$table = $subObj;
                  break 3;
                }
              }
            }
          }
        }
      }
    }

    $this->_data = $mainData;
  }

  /*
  |--------------------------------------------------------------------------
  | belongsTo @params (object) $model | (string) $mainFk
  |--------------------------------------------------------------------------
  |
  | pre define relation in model 
  | get sub model object and foreign key of sub model as arguments
  | set result data to property data as an object
  |
  */

  protected function belongsTo($model, $mainFk)
  {
    $table = $model->_table;
    $subPk = $model->_primaryKey;
    $queryData = $this->queryWithBelong($table, $mainFk, $subPk);
    $mainData = $queryData['main'];
    $subData = $queryData['sub'];

    foreach($mainData as $mainIndex => $mainObj) {
      $mainObj->$table = null;
      foreach($mainObj as $mainKey => $mainValue) {
        if ($mainKey == $mainFk) {
          foreach ($subData as $subIndex => $subObj) {
            foreach ($subObj as $subKey => $subValue) {
              if ($subKey == $subPk) {
                if ($mainValue == $subValue) {
                  $mainObj->$table = $subObj;
                  break 3;
                }
              }
            }
          }
        }
      }
    }

    $this->_data = $mainData;
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

  private function getColumnParamUpdate($data)
  {
    $strColumn = "";
    $i = 0;
    foreach ($data as $key => $value) {
      if ($i != 0) {
        $strColumn .= ",";
      }
      $strColumn .= $key;
      $strColumn .= "=";
      $strColumn .= ":";
      $strColumn .= $key;

      $this->_queryBuilder['bindedValue'][":$key"] = $value;
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

  private function getColumnInsert($data)
  {
    $strColumn = "";
    $i = 0;
    foreach ($data as $key => $value) {
      if ($i != 0) {
        $strColumn .= ",";
      }
      $strColumn .= $key;

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

  private function getColumnParamInsert($data)
  {
    $strColumnParams = "";
    $i = 0;
    foreach ($data as $key => $value) {
      if ($i != 0) {
        $strColumnParams .= ",";
      }
      $strColumnParams .= ":";
      $strColumnParams .= $key;

      $this->_queryBuilder['bindedValue'][":$key"] = $value;
      $i++;
    }

    return $strColumnParams;
  }

  /*
  |--------------------------------------------------------------------------
  | queryBuilder
  |--------------------------------------------------------------------------
  |
  | building to sql command for execution
  | use to build a chain select, where, join commands
  | @return (string) $sql
  |
  */

  private function queryBuilder() 
  {
    $sqlSelect = " ";
    $sqlWhere = " ";
    $sqlJoin = " ";
    $sqlOrder = " ";
    $action = $this->_queryBuilder['action'];

    if ( count($this->_queryBuilder['select']) > 0) {
      $sqlSelect .= " SELECT ";
      $sqlSelect .= implode(",", $this->_queryBuilder['select']);
    } 
    else {
      $sqlSelect .= " SELECT * ";
    }

    if ( count($this->_queryBuilder['join']) > 0) {
      $sqlJoin .= implode(",", $this->_queryBuilder['join']);
    }

    if ( count($this->_queryBuilder['where']) == 1) {
      $condWhere = $this->_queryBuilder['where']['0'];
      $sqlWhere .= " WHERE $condWhere ";
    } 
    elseif ( count($this->_queryBuilder['where']) > 1) {      
      $sqlWhere .= " WHERE ";
      $sqlWhere .= implode("AND", $this->_queryBuilder['where']);
    }

    $sqlLimit = ($this->_queryBuilder['limit'] !== null) 
      ? " LIMIT " . $this->_queryBuilder['limit']
      : "";

    $sqlOffset = ($this->_queryBuilder['offset'] !== null) 
      ? " OFFSET " . $this->_queryBuilder['offset']
      : "";

    if ( count($this->_queryBuilder['orderBy']) == 1) {
      $condOrder = $this->_queryBuilder['orderBy'][0];
      $sqlOrder .= " ORDER BY $condOrder ";
    } 
    elseif ( count($this->_queryBuilder['orderBy']) > 1) {      
      $sqlOrder .= " ORDER BY ";
      $sqlOrder .= implode(",", $this->_queryBuilder['orderBy']);
    }

    if ($action === 'select') {
      $sql = " $sqlSelect FROM $this->_table $sqlJoin $sqlWhere $sqlOrder $sqlLimit $sqlOffset";
    } 
    elseif ($action === 'insert') {
      $sqlSetInsert = $this->getColumnInsert((array) $this->_data);
      $sqlSetInsertParams = $this->getColumnParamInsert((array) $this->_data);
      $sql = " INSERT INTO $this->_table($sqlSetInsert) VALUES($sqlSetInsertParams) ";
    }
    elseif ($action === 'update') {
      $sqlSetUpdate = $this->getColumnParamUpdate((array) $this->_data); 
      $sql = " UPDATE $this->_table SET $sqlSetUpdate $sqlWhere ";
    }
    elseif ($action === 'delete') {
      $sql = " DELETE FROM $this->_table $sqlWhere ";
    }

    return $sql;
  }

  /*
  |--------------------------------------------------------------------------
  | queryWith @params (string) $table | (string) $primaryKey
  |--------------------------------------------------------------------------
  |
  | query the with() function with given arguments
  | need to define relation in model first
  | @return (array) ['main', 'sub']
  |
  */

  private function queryWith($table, $primaryKey)
  {
    $sql = $this->queryBuilder(); 
    $pk = $this->_primaryKey;

    if ($this->_queryBuilder['limit'] === 1) {
      $mainData = $this->execute($sql, null, 'fetchAll');
      $mainData_ids[0] = $mainData[0]->$pk;
    }
    else {
      $mainData = $this->execute($sql, null, 'fetchAll');
      $mainData_ids = array_map( function($mainDataObj) use ($pk) {
        return $mainDataObj->$pk;
      }, $mainData);
    }

    if ($mainData_ids) {
	    $param_ids = $this->getColumnParamInsert($mainData_ids);
	    $sql = " SELECT $table.* FROM $this->_table 
	      LEFT JOIN $table ON $this->_table.$this->_primaryKey = $table.$primaryKey 
	      WHERE $table.$primaryKey IN ($param_ids) ";
	    $subData = $this->execute($sql, $mainData_ids, 'fetchAll');
	  } 
	  else {
	  	$mainData = array();
	  	$subData = array();	
	  }

    if ($this->_mainData === null)
      $this->_mainData = $mainData;

    $mainData = $this->_mainData;

    return array('main' => $mainData, 'sub' => $subData);
  }

  /*
  |--------------------------------------------------------------------------
  | queryWithBelong @params (string) $table | (string) $mainFk | (string) $subPk
  |--------------------------------------------------------------------------
  |
  | query the with() function with given arguments
  | need to define relation in model first
  | @return (array) ['main', 'sub']
  |
  */

  private function queryWithBelong($table, $mainFk, $subPk)
  {
    $sql = $this->queryBuilder(); 

    if ($this->_queryBuilder['limit'] === 1) {
      $mainData = $this->execute($sql, null, 'fetchAll');
      $mainData_ids[0] = $mainData[0]->$mainFk;
    }
    else {
      $mainData = $this->execute($sql, null, 'fetchAll');
      $mainData_ids = array_map( function($mainDataObj) use ($mainFk) {
        return $mainDataObj->$mainFk;
      }, $mainData);
    }

    if ($mainData_ids) {
	    $param_ids = $this->getColumnParamInsert($mainData_ids);
	    $sql = " SELECT $table.* FROM $this->_table 
	      LEFT JOIN $table ON $this->_table.$mainFk = $table.$subPk
	      WHERE $table.$subPk IN ($param_ids) ";
	    $subData = $this->execute($sql, $mainData_ids, 'fetchAll');
    } 
    else {
	  	$mainData = array();
	  	$subData = array();	
	  }

    if ($this->_mainData === null)
      $this->_mainData = $mainData;

    $mainData = $this->_mainData;

    return array('main' => $mainData, 'sub' => $subData);
  }

  /*
  |--------------------------------------------------------------------------
  | execute @params (string) $sql | (array, null) $bindedValue | (string, null) $opt
  |--------------------------------------------------------------------------
  |
  | after build up query builder
  | this method will execute the sql command
  | reset all the query builder
  | @return (object) $this->_data
  |
  */

  private function execute($sql, $bindedValue = null, $opt = null)
  {
    if ($bindedValue === null)
      $bindedValue = $this->_queryBuilder['bindedValue'];

    $this->_con = Database::connect();

    try {
      $this->_statement = $this->_con->prepare($sql);
      $this->_statement->execute($bindedValue);

      if ($this->_queryBuilder['action'] === 'insert')
        $this->_lastInsertId = $this->_con->lastInsertId();

      if ($opt === 'fetchAll')
        $this->_data = $this->_statement->fetchAll();

      if ($opt === 'fetch')
        $this->_data = $this->_statement->fetch();
    }
    catch (Exception $err) { new CustomExceptionMessage($err); }

    $this->_con = null;

    $this->_queryBuilder = array(
      'select' => array(),
      'where' => array(),
      'join' => array(),
      'orderBy' => array(),
      'bindedValue' => array(),
      'with' => array(),
      'limit' => null,
      'offset' => null,
      'action' => 'select'
    );

    return $this->_data;
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

  private function strEscape($rawStr)
  {
    $rawStr = str_replace( array(";", ','), "", $rawStr);
    $rawStr = str_replace(
      array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), 
      array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), 
      $rawStr); 

    return $rawStr;
  }
}