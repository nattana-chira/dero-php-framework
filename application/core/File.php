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

	/*
	|--------------------------------------------------------------------------
	| move @params (string) $old_path, (string) $new_path
	|--------------------------------------------------------------------------
	|
	| move file to the new path
	|
	*/
	public static function move($old_path, $new_path)
	{
		$old_path = $_SERVER['DOCUMENT_ROOT'] . $old_path;
		$new_path = $_SERVER['DOCUMENT_ROOT'] . $new_path;
		rename($old_path, $new_path);
	}

}