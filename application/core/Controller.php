<?php 

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Base Controller
|--------------------------------------------------------------------------
|
| define functions for controller to inherits
| render function require a view with pass data
| json function return a json with header set json
|
*/

use Core\Session;
use Core\ErrorBag;

class Controller 
{

  /*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $_session = an object of class Session
  | 
  */

  private $_session;

  /*
  |--------------------------------------------------------------------------
  | construct
  |--------------------------------------------------------------------------
  |
  | inject session class to attribute $_session
  |
  */

  function __construct()
  {
    $this->_session = new Session();
  }

  /*
  |--------------------------------------------------------------------------
  | render @params (string) $_filename_ | (array) $_dataset_ = null
  |--------------------------------------------------------------------------
  |
  | require a view with @param filename and define a data from dataset
  | delete unnessesary data variable
  |
  */

  public function render($_filename_, $_dataset_ = null, $_customPath_ = 'Views') 
  {
    $_dumpArray_ = explode('/', $_customPath_);
    $_customPath_ = '';
    foreach ($_dumpArray_ as $_index_ => $_data_) {
      if ( $_data_ !== '' || $_data_ !== null) {
        $_customPath_ .= $_data_;
        $_customPath_ .= '/';
      }
    }

    if ($_dataset_ !== null) {
      foreach ($_dataset_ as $_index_ => $_data_) {
        $$_index_ = $_data_;  
      }
    }

    $errors = new ErrorBag();
    if (isset($_SESSION['errors'])) {
      foreach ($_SESSION['errors'] as $_index_ => $_data_) {
        $errors->$_index_ = $_data_;
      }
    }

    $_customPath_ .= $_filename_;

    unset($_data_);
    unset($_dataset_);
    unset($_index_);
    unset($_dumpArray_);
    unset($_filename_); 
    
    require_once (__DIR__ . '/global/Session.php');
    require_once (__DIR__ . '/../app/'.  $_customPath_);

    $this->_session->destroyFlash();  
  }

  /*
  |--------------------------------------------------------------------------
  | json @params (array) $data
  |--------------------------------------------------------------------------
  |
  | set header to return json & utf-8
  | return data with json encode
  |
  */
  
  public function json($data, $status = 200) {
    header('Content-Type: application/json;charset=utf-8');
    http_response_code($status);
    return json_encode($data);
  }

  /*
  |--------------------------------------------------------------------------
  | redirect @params (string) $url
  |--------------------------------------------------------------------------
  |
  | set header to redirect url
  | and stop working on scirpt
  |
  */

  public function redirect($url) {
    $this->_session->destroyFlash(); 
    header("Location: $url");
    die();
  }

  /*
  |--------------------------------------------------------------------------
  | session
  |--------------------------------------------------------------------------
  |
  | access object session in attribute $_session
  |
  */

  public function session() 
  {
    return $this->_session;
  }
}