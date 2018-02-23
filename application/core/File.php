<?php

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : File
|--------------------------------------------------------------------------
|
| file manupilator class
|
*/

class File
{
	
	/*
	|--------------------------------------------------------------------------
	| delete @params (string) $path
	|--------------------------------------------------------------------------
	|
	| check if file exist in dir and then delete it
	|
	*/

	public static function delete($path)
	{
		if (substr($path, 0, 1) === '/')
			$path = substr($path, 1);
		
		if (file_exists($path))
			unlink($path);
	}

}