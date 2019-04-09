# Dero php framework
Dero is a mini custom php framework with MVC structure for PHP to build basic restful api website

Documentation page >> https://nattana-chira.github.io/dero-php-framework

included libs 
  - jquery
  - bootstrap 3

project structure

- backend
  - config
    - database.php // ** database config
    - routes.php  // ** route config
  - controllers
    - UserController.php // ** boilerplate controller
  - core
    - Controller.php
    - Database.php
    - Model.php
    - Request.php
    - Route.php
  - models
    - User.php // ** boilerplate model
  - views
    - 404.php // ** 404 not found page
    - home.php // ** boilerplate home view
- public
  - css
    - bootstrap.min.css
  - images
  - js
    - bootstrap.min.js
    - jquery-3.2.1.min.js
  - htaccess  // ** change htaccess to .htaccess
  - index.php // ** initialize application
  - robot.txt
