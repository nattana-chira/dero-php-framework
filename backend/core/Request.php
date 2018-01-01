<?php

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

class Request 
{
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
    $this->decode();
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
    $this->_data_ = explode("=", urldecode($this->_data_));

    foreach ($this->_data_ as $index => $value) {
      if ($index % 2 == 0)
        $this->$value = $this->_data_[$index+1];
    }

    unset($this->_data_);
  }
}