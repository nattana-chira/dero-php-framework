<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
  protected $table = 'users';
  protected $primaryKey = 'id';
}