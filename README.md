# phpframework
custom php framework with MVC structure

included libs 
  - jquery
  - bootstrap 3

project structure

  backend
    v config
      > database.php // database config
      > routes.php  // route config
    v controllers
      > UserController.php // boilerplate controller
    v core
      > Controller.php
      > Database.php
      > Model.php
      > Request.php
      > Route.php
    v models
      > User.php // boilerplate model
    v views
      > 404.php // 404 not found page
      > home.php // boilerplate home view
  public
    v css
      > bootstrap.min.css
    v images
    v js
      > bootstrap.min.js
      > jquery-3.2.1.min.js
    > htaccess  // change htaccess to .htaccess
    > index.php // initialize application
    > robot.txt
