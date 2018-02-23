<?php 

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Session
|--------------------------------------------------------------------------
|
| define functions for controller to use session as its own attributes
| encapsulates all the function used with $_SESSION
|
*/

class Session 
{

  /*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $_flash = an array of flash session on single request
  | 
  */

	public static $_flash = array();

  /*
  |--------------------------------------------------------------------------
  | __construct
  |--------------------------------------------------------------------------
  |
  | start php session
  | 
  */

	function __construct()
  {
    // try {
    //   session_start();
    // } catch (Exception $e) {}

    if(session_id() == '') {
        session_start();
    }
  }

  /*
  |--------------------------------------------------------------------------
  | all
  |--------------------------------------------------------------------------
  |
  | retrieves all of the session variables
  | @return (array)
  | 
  */

  public function all()
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

  public function get($key)
  {
  	return $_SESSION[$key];
  }

  /*
  |--------------------------------------------------------------------------
  | put @params (string) $key | (any) $value
  |--------------------------------------------------------------------------
  |
  | set the value to session by passing 2 args key and value
  | 
  */

  public function put($key, $value)
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

  public function push($key, $value)
  {
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

  public function pull($key)
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

  public function has($key)
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

  public function exists($key)
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

  public function forget($key)
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

  public function flush()
  {
  	$_SESSION = array();
  }

  /*
  |--------------------------------------------------------------------------
  | flash @params (string) $key | (any) $value
  |--------------------------------------------------------------------------
  |
  | set the flash session that belongs only to next respone
  | the flash session will be destroyed immidiately
  | @return (any)
  | 
  */

  public function flash($key, $value)
  {
  	$_SESSION[$key] = $value;

    if (! isset($_SESSION['flash']))
      $_SESSION['flash'] = array();

    $_SESSION['flash'][$key] = 1;
  }

  /*
  |--------------------------------------------------------------------------
  | destroyFlash
  |--------------------------------------------------------------------------
  |
  | destroy all the flash session that has been set
  | 
  */

  public function destroyFlash()
  {
    if ( isset($_SESSION['flash']) && count($_SESSION['flash']) > 0) {
      foreach ($_SESSION['flash'] as $key => $value) {
        if ($_SESSION['flash'][$key] == 0) {
          unset($_SESSION['flash'][$key]);
          unset($_SESSION[$key]);
        }
        else 
          $_SESSION['flash'][$key] = $_SESSION['flash'][$key] - 1;
      }
    }
    else 
      unset($_SESSION['flash']);

    if (isset($_SESSION['flash']) && count($_SESSION['flash']) === 0)
      unset($_SESSION['flash']);
  }
}