<?php 

/*
|--------------------------------------------------------------------------
| Global : Session
|--------------------------------------------------------------------------
|
| define functions for view to use session as its own attributes
| encapsulates all the function used with $_SESSION
|
*/

class Session 
{
  /*
  |--------------------------------------------------------------------------
  | all
  |--------------------------------------------------------------------------
  |
  | retrieves all of the session variables
  | @return (array)
  | 
  */

  public static function all()
  {
  	return $_SESSION;
  }

  /*
  |--------------------------------------------------------------------------
  | get @params (string) $key
  |--------------------------------------------------------------------------
  |
  | retrieves session value by $key given in args
  | @return (any)
  | 
  */

  public static function get($name)
  {
  	return $_SESSION[$name];
  }

  /*
  |--------------------------------------------------------------------------
  | put @params (string) $key | (any) $value
  |--------------------------------------------------------------------------
  |
  | set the value to session by passing 2 args key and value
  | 
  */

  public static function put($key, $value)
  {
  	$_SESSION[$key] = $value;
  }

  /*
  |--------------------------------------------------------------------------
  | push @params (string) $key | (any) $value
  |--------------------------------------------------------------------------
  |
  | push a value to a session that is an array
  | @return (any)
  | 
  */

  public static function push($key, $value)
  {
  	$_SESSION[$key] = $value;
  	array_push($_SESSION[$key], $value);
  }

  /*
  |--------------------------------------------------------------------------
  | pull @params (string) $key
  |--------------------------------------------------------------------------
  |
  | retrives the value of session and also delete it
  | @return (any)
  | 
  */

  public static function pull($key)
  {
  	$data = $_SESSION[$key];
  	unset($_SESSION[$key]);

  	return $data;
  }

  /*
  |--------------------------------------------------------------------------
  | has @params (string) $key
  |--------------------------------------------------------------------------
  |
  | the check if value of session is set and value is not null
  | @return (boolean)
  | 
  */

  public static function has($key)
  {
  	return (isset($_SESSION[$key]) && $_SESSION[$key] !== null);
  }

  /*
  |--------------------------------------------------------------------------
  | exists @params (string) $key
  |--------------------------------------------------------------------------
  |
  | the check if the session exist or not
  | @return (boolean)
  | 
  */

  public static function exists($key)
  {
  	return (isset($_SESSION[$key]));
  }

  /*
  |--------------------------------------------------------------------------
  | forget @params (string) $key
  |--------------------------------------------------------------------------
  |
  | unset the value of the session
  | 
  */

  public static function forget($key)
  {
  	unset($_SESSION[$key]);
  }

  /*
  |--------------------------------------------------------------------------
  | flush
  |--------------------------------------------------------------------------
  |
  | flush all the session away turning session to an empty array
  | 
  */

  public static function flush()
  {
  	$_SESSION = array();
  }
}