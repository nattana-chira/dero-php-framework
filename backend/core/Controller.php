<?php 

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

class Controller 
{

  /*
  |--------------------------------------------------------------------------
  | render @params (string) $_filename_ | (array) $_dataset_ = null
  |--------------------------------------------------------------------------
  |
  | require a view with @param filename and define a data from dataset
  | delete unnessesary data variable
  |
  */

  public function render($_filename_, $_dataset_ = null) 
  {
    if ($_dataset_ !== null) {
      foreach ($_dataset_ as $_index_ => $_data_) {
        $$_index_ = $_data_;  
      }
    }
    unset($_data_);
    unset($_dataset_);
    unset($_index_);
    
    require_once (__DIR__ .'/../views/'.$_filename_);
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
}