<?php 

use Core\Controller;
use Core\Request;

class PageController extends Controller 
{
  public function index()
  {
    return $this->render('index.php');
  }
}