<?php

namespace Core; 

/*
|--------------------------------------------------------------------------
| Core : ErrorBag
|--------------------------------------------------------------------------
|
| errorbag builder use to define or manupilate the error
| and be use in view automatic define as var $errors
| and set in flash session name errors
|
*/

class ErrorBag
{

	/*
	|--------------------------------------------------------------------------
	| any
	|--------------------------------------------------------------------------
	|
	| find out if there is any errors in the bag
	| @return (boolean)
	|
	*/

	public function any()
	{
		$error = null;
		foreach ($this as $key => $value) {
			$error[$key] = $value;
		}

		return ($error !== null);
	}

	/*
	|--------------------------------------------------------------------------
	| all
	|--------------------------------------------------------------------------
	|
	| retrieve all the errors in the bag
	| @return (array)
	|
	*/

	public function all()
	{
		foreach ($this as $key => $value) {
			$data[$key] = $value;
		}

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| get @params (string) $key
	|--------------------------------------------------------------------------
	|
	| get the specific error by given args
	| @return (array)
	|
	*/

	public function get($key)
	{
		return $this->$key;
	}

	/*
	|--------------------------------------------------------------------------
	| first @params (string) $key
	|--------------------------------------------------------------------------
	|
	| get first error from specific error by given args 
	| @return (string)
	|
	*/

	public function first($key)
	{
		$data = $this->$key;
		return reset($data);
	}

	/*
	|--------------------------------------------------------------------------
	| has @params (string) $key
	|--------------------------------------------------------------------------
	|
	| to check if the specific error exists in the bag
	| @return (boolean)
	|
	*/

	public function has($key)
	{
		return isset($this->$key);
	}
}