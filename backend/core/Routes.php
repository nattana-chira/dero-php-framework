<?php 

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

/* # backend/core/Request */
require_once (__DIR__ .'/Request.php');

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

  public function get($request_routes, $controller, $method)
  {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      self::requestWithBody($request_routes, $controller, $method);
    }
  }

  public function post($request_routes, $controller, $method)
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST")
      self::requestWithBody($request_routes, $controller, $method);
  }

  public function put($request_routes, $controller, $method)
  {
    if ($_SERVER["REQUEST_METHOD"] == "PUT")
      self::requestWithBody($request_routes, $controller, $method);
  }

  public function delete($request_routes, $controller, $method)
  {
    if ($_SERVER["REQUEST_METHOD"] == "DELETE")
      self::requestWithBody($request_routes, $controller, $method);
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
  
  private function getCurrentUri ()
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

  private function requestWithBody($request_routes, $controller, $method)
  {
    $current_uri = static::getCurrentUri();
    $request = false; 
    if (file_get_contents('php://input')) $request = new Request(file_get_contents('php://input'));

    $pos1 = strpos($request_routes, '{');
    $pos2 = strpos($request_routes, '}');

    if ($pos1 && $pos2) {
      $param_uri = substr($current_uri, $pos1);
      $request_routes = substr($request_routes, 0, $pos1);
      $current_uri = substr($current_uri, 0, $pos1);

      if ($request_routes === $current_uri) { 
        if ( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );

        require_once (__DIR__ .'/../controllers/'.$controller.'.php');
        $controller = new $controller();
        if ($request) echo $controller->$method($request, $param_uri);
        else echo $controller->$method($param_uri);

        static::$num++;
      }
    }
    else if ($request_routes === $current_uri) {
      if ( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
      require_once ('__DIR__./../controllers/'.$controller.'.php');

      $controller = new $controller();
      if ($request) echo $controller->$method($request);
      else echo $controller->$method();

      static::$num++;
    }
  }
}


