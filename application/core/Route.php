<?php 

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Routes
|--------------------------------------------------------------------------
|
| - get current uri and validate incoming request
| - if incoming request match routes
| - require a controller and echo object method
|
*/

use Core\Request;

class Route 
{
  // num for checking for not found page condition
  public static $num = 0;

  /*
  |--------------------------------------------------------------------------
  | route @params (string) $request_routes | (string) $controller | (string) $method
  |--------------------------------------------------------------------------
  |
  | check what type of request and check if there is any params
  | include a file by param $controller name
  | create object from controller and call to method by param $method
  |
  */

  public static function get($request_routes, $controller, $method, $customPath = 'Controllers')
  {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      self::requestWithBody($request_routes, $controller, $method, $customPath);
    }
  }

  public static function post($request_routes, $controller, $method, $customPath = 'Controllers')
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST")
      self::requestWithBody($request_routes, $controller, $method, $customPath);
  }

  public static function put($request_routes, $controller, $method, $customPath = 'Controllers')
  {
    if ($_SERVER["REQUEST_METHOD"] == "PUT")
      self::requestWithBody($request_routes, $controller, $method, $customPath);
  }

  public static function delete($request_routes, $controller, $method, $customPath = 'Controllers')
  {
    if ($_SERVER["REQUEST_METHOD"] == "DELETE")
      self::requestWithBody($request_routes, $controller, $method, $customPath);
  }

  /*
  |--------------------------------------------------------------------------
  | getCurrentUri
  |--------------------------------------------------------------------------
  |
  | get the current uri from the browser into an array
  | implode it into a string
  | get rid of invalid url and trim the string
  | @return (string) $uri
  |
  */
  
  private static function getCurrentUri ()
  {
    $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
    if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
    $uri = '/' . trim($uri, '/');
    return $uri;
  }

  /*
  |--------------------------------------------------------------------------
  | requestWithBody @params (string) $request_routes | (string) $controller | (string) $method
  |--------------------------------------------------------------------------
  |
  | handle all the request from the server
  | check for params and include file by condition
  |
  */

  private static function requestWithBody($request_routes, $controller, $method, $customPath)
  {
    $current_uri = self::getCurrentUri();
    $request = false; 

    if (file_get_contents('php://input')) {
      $request = new Request(file_get_contents('php://input'));
      $request->decode();
    } 
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (! empty($_POST)) {
        $request = new Request($_POST);
        $request->decodeOpt();
      }
    }

    if (strpos($request_routes, '{') !== false) {
      $array_request_routes = explode('/', $request_routes);
      $array_current_uri = explode('/', $current_uri);

      if ( count($array_request_routes) === count($array_current_uri)) {
        foreach ($array_request_routes as $i => $value) {
          if (strpos($value, '{') !== false) {
            $param_uri = $array_current_uri[$i];
            unset($array_request_routes[$i]);
            unset($array_current_uri[$i]);
          }
        }
      }

      $request_routes = implode('/', $array_request_routes);
      $current_uri = implode('/', $array_current_uri);

      if ($request_routes === $current_uri) 
        static::initController($customPath, $controller, $method, $request, $param_uri);
    }
    else if ($request_routes === $current_uri) {
      static::initController($customPath, $controller, $method, $request);
    }
  }

  /*
  |--------------------------------------------------------------------------
  | initController @params (string) $customPath | (string) $controller |
  | (boolean, object) $request | (boolean, string) $param_uri
  |--------------------------------------------------------------------------
  |
  | handle how controller will be initialized
  | require controller file and run the method
  | print return data
  |
  */

  public static function initController(
    $customPath, $controller, $method, $request, $param_uri = false)
  {
    $customPath = static::customPath($customPath);
    require_once (__DIR__ . '/../app/' . $customPath . $controller . '.php');

    $controller = new $controller();

    if ($request && $param_uri !== false)
      $returnData = $controller->$method($request, $param_uri);
    else if ($param_uri !== false)
      $returnData = $controller->$method($param_uri);
    else if ($request) 
      $returnData = $controller->$method($request);
    else 
      $returnData = $controller->$method();

    if ( is_string($returnData))
      echo $returnData;
    else
      print_r($returnData);

    static::$num++;
  }

  /*
  |--------------------------------------------------------------------------
  | customPath @params (string) $customPath
  |--------------------------------------------------------------------------
  |
  | in case of use define custom path for controller
  | reformat the path given to prevent error
  |
  */

  public static function customPath($customPath)
  {
    if ( !defined( __DIR__ ) ) 
      define( __DIR__, dirname(__FILE__) );

    $dumpArray = explode('/', $customPath);
    $customPath = '';

    foreach ($dumpArray as $index => $value) {
      if ( $value !== '' || $value !== null) {
        $customPath .= $value;
        $customPath .= '/';
      }
    }

    return $customPath;
  }

}
