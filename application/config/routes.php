<?php 

namespace Config;

/*
|--------------------------------------------------------------------------
| Config : Routes
|--------------------------------------------------------------------------
|
| you can define your routes for application here
| @param  string $route , string $controller, string $method
| @return echo from a controller
| 
| for example :
| Route::get('/users/create', 'UserController', 'create');
|
*/

use Core\Route;

/*
|--------------------------------------------------------------------------
| # Define your routes under the lines comment #
|--------------------------------------------------------------------------
*/

Route::get('/', 'PageController', 'index');

/*
|--------------------------------------------------------------------------
| 404 Not Found
|--------------------------------------------------------------------------
|
| define error routes if page not found
|
*/

if (Route::$num == 0) require_once (__DIR__ . '/../app/Views/404.php');





