<?php

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : File
|--------------------------------------------------------------------------
|
| an object of file in body from post request
| use to store the file to system or something
| and also can manipulate the data of file
|
*/

class RequestFile
{

	/*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $_inputName = name of the file input
  | 
  */

	private $_inputName;

  /*
  |--------------------------------------------------------------------------
  | __construct @param (string) $name
  |--------------------------------------------------------------------------
  |
  | set each of value of $_FILES to as attribute
  | set attribute $this->_inputName to name of the file input
  |
  */

	function __construct($name) 
	{
		foreach ($_FILES[$name] as $key => $value) {
			$this->$key = $value;
		}

		$this->_inputName = $name;
	}

	/*
  |--------------------------------------------------------------------------
  | store @param (string) $dir
  |--------------------------------------------------------------------------
  |
  | store the file to the system 
  | specify the path to save if destination folder not exist
  | system will create new directory
  | @return (string) $filepath | success
  | @return false | fail
  |
  */

	public function store($dir)
	{
		if ( file_exists( $_FILES[$this->_inputName]['tmp_name']))
    {
      $uploaddir    = "$dir/";
      $filename     = basename($_FILES[$this->_inputName]['name']);
      $extension    = pathinfo($filename, PATHINFO_EXTENSION);
      $newfilename 	= $this->generateNumber(50).'.'.$extension;
      $filepath 		= $uploaddir.$newfilename;

      if (! is_dir($uploaddir)) {
        $this->createDir($uploaddir);
      }

      if ( move_uploaded_file($_FILES[$this->_inputName]['tmp_name'], $filepath ))
        return "/".$filepath;
      
      return false;
    }
	}

	/*
  |--------------------------------------------------------------------------
  | storeAs @param (string) $dir | (string) $inputFilename
  |--------------------------------------------------------------------------
  |
  | store the file to the system 
  | specify the path to save if destination folder not exist
  | and set the name file that saved equal to given args
  | @return (string) $filepath | success
  | @return false | fail
  |
  */

	public function storeAs($dir, $inputFilename)
	{
		if ( file_exists( $_FILES[$this->_inputName]['tmp_name']))
    {
      $uploaddir    = "$dir/";
      $filename     = basename($_FILES[$this->_inputName]['name']);
      $extension    = pathinfo($filename, PATHINFO_EXTENSION);
      $newfilename 	= $inputFilename.'.'.$extension;
      $filepath 		= $uploaddir.$newfilename;

      if (! is_dir($uploaddir)) {
        $this->createDir($uploaddir);
      }

      if ( move_uploaded_file($_FILES[$this->_inputName]['tmp_name'], $filepath ))
        return "/".$filepath;
      
      return false;
    }
	}

	/*
  |--------------------------------------------------------------------------
  | store @param (string) $maindir | (array) $thumbs
  |--------------------------------------------------------------------------
  |
  | store the file to the system 
  | specify the path to save if destination folder not exist
  | and also save thumbnail to another path with another size
  | @return (array) $paths | success
  | @return false | fail
  |
  */

	public function storeWithThumb($maindir, $thumbs, $saveMain = true)
	{
	  if  ( isset($_FILES[$this->_inputName]) && $_FILES[$this->_inputName]['tmp_name'] !== '') {
      $uploaddir = "$maindir/";

      if (! is_dir($uploaddir)){
        $this->createDir($uploaddir);
      }

      $prefix = date("d-m-Y").'_'.$this->generateNumber(40);
      list(,,$extension) = getimagesize($_FILES[$this->_inputName]['tmp_name']);
      $extension = image_type_to_extension($extension);
      $mainpath = $uploaddir.$prefix.$extension;

      if (move_uploaded_file($_FILES[$this->_inputName]['tmp_name'], $mainpath)) {
	      $imgcreate = 'imagecreatefrom'.$extension;
	      $imgcreate = str_replace('.','',$imgcreate);
	      $img = $imgcreate($mainpath);

	      $paths['main'] = "/".$mainpath;
      	$paths['thumbnail'] = array();

	      foreach($thumbs as $key => $value) {
	        $width = imagesx( $img );
	        $height = imagesy( $img );

          if ($value > 1600) $value = 1600;

	        $new_width = $value;
	        $new_height = floor( $height * ( $value / $width ) );

	        $tmp_img = imagecreatetruecolor( $new_width, $new_height );
	        imagealphablending( $tmp_img, false );
	        imagesavealpha( $tmp_img, true );
	        imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

	        $key .= "/";
        	if (! is_dir($key)) {
            $this->createDir($key);
          }

	        $thumbpath = $key.$prefix.'_'.$value.$extension;

          if ($extension == '.png')
            $extension = '.jpeg';

	        $imgext = 'image'.$extension;
	        $imgext = str_replace('.', '', $imgext);
	        $imgext( $tmp_img, $thumbpath );
	        array_push($paths['thumbnail'], "/".$thumbpath);
      	}

        if ($saveMain === false) 
          unlink($mainpath);

      	return $paths;
      }
    }

    return false;
	}


  private function createDir($uploaddir)
  {
    $oldmask = umask(0);
    mkdir($uploaddir, 0777);
    umask($oldmask);
  }

	/*
  |--------------------------------------------------------------------------
  | getClientOriginalName
  |--------------------------------------------------------------------------
  |
  | get the original file name user uploaded
  | * filename is including extension *
  |
  */

	public function getClientOriginalName() 
	{
		return basename($_FILES[$this->_inputName]['name']);
	}

	/*
  |--------------------------------------------------------------------------
  | getClientOriginalExtension
  |--------------------------------------------------------------------------
  |
  | get the original file extension user uploaded
  | * . is not including to extension *
  |
  */

	public function getClientOriginalExtension()
	{
		return pathinfo( basename($_FILES[$this->_inputName]['name']), PATHINFO_EXTENSION);
	}

	/*
  |--------------------------------------------------------------------------
  | getClientSize() 
  |--------------------------------------------------------------------------
  |
  | get the original file size user uploaded
  | * return as byte unit *
  |
  */

	public function getClientSize() 
	{
		return $this->size;
	}

	/*
  |--------------------------------------------------------------------------
  | generateNumber @params (string) $length
  |--------------------------------------------------------------------------
  |
  | generate random long string contain 0-9 , a-z with specific length
  | @return (string) $randomKey
  |
  */

	private function generateNumber($length) {
    $randomKey = '';
    $secret = array_merge(range(0, 9), range('a', 'z'));
    for ($i = 0; $i < $length; $i++) {
        $randomKey .= $secret[array_rand($secret)];
    }

    return $randomKey;
  }
}