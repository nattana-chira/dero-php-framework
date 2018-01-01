<?php

require_once (__DIR__ .'/../core/Model.php');

class User extends Model
{
  public $table = 'users';
  public $primaryKey = 'id';
}