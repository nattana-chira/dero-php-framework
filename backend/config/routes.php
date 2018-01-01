<?php 

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
| Route::request('/users/create', 'UserController', 'create');
|
*/

/* # backend/core/Route */
require_once (__DIR__ .'/../core/Routes.php');


/*
|--------------------------------------------------------------------------
| 404 Not Found
|--------------------------------------------------------------------------
|
| define error routes if page not found
|
*/

if (Route::$num == 0) require_once ('404.php');





