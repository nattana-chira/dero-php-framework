<?php

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Request
|--------------------------------------------------------------------------
|
| - incoming post/put/patch/delete requests 
| - decodeurl http encoded
| - cast http data array to object
|
*/

use Core\RequestFile;

class Request 
{
  
  /*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $_data_ = store all the request body to this var
  | 
  */

  public $_data_;

  /*
  |--------------------------------------------------------------------------
  | __construct @param (string) $input
  |--------------------------------------------------------------------------
  |
  | - set input data to props _data_
  | - call to function decode()
  |
  */

  function __construct($input) 
  {
    $this->_data_ = $input;
  }

  /*
  |--------------------------------------------------------------------------
  | decode
  |--------------------------------------------------------------------------
  |
  | - replace & with = form urlencode
  | - explode to array with =
  | - loop throuh http array an set to props
  | - unset unused props 
  |
  */

  public function decode() 
  {
    $this->_data_ = str_replace("&", "=", $this->_data_);
    $this->_data_ = explode("=", $this->_data_);

    foreach ($this->_data_ as $index => $value) {
      if ($index % 2 == 0)
        $this->$value = urldecode($this->_data_[$index+1]);
    }

    unset($this->_data_);
  }

  /*
  |--------------------------------------------------------------------------
  | decodeOpt() 
  |--------------------------------------------------------------------------
  |
  | - loop throuh http array an set to props
  | - unset unused props 
  |
  */

  public function decodeOpt() 
  {
    foreach ($this->_data_ as $key => $value) {
      $this->$key = $value;   
    }

    unset($this->_data_);
  }

  /*
  |--------------------------------------------------------------------------
  | file @params (string) $name
  |--------------------------------------------------------------------------
  |
  | return the file uploaded instance by given args
  |
  */

  public function all()
  {
    foreach($this as $key => $value) {
      $request[$key] = $value; 
    }

    return $request;
  }

  public function file($name)
  {
    return new RequestFile($name);
  }

  /*
  |--------------------------------------------------------------------------
  | has @params (string) $name
  |--------------------------------------------------------------------------
  |
  | check if input exist in request body by given args
  | @return (boolean)
  |
  */

  public function has($name)
  {
    return isset($this->$name);
  }

  /*
  |--------------------------------------------------------------------------
  | hasFile @params (string) $name
  |--------------------------------------------------------------------------
  |
  | check if the file uploaded exist by given args
  | @return (boolean)
  |
  */

  public function hasFile($name)
  {
    return file_exists($_FILES[$name]['tmp_name']);
  }
}