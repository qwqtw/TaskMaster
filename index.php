<?php
// Load the composer required libraries
require "vendor/autoload.php";

// Load the framework
$f3 = Base::instance();
$f3->config("config.ini");

// Database connection information
$f3->config("access.ini");

// Routes
// index.html
$f3->route("GET @home: /", "LoginController->render");
$f3->route("POST @home: /", "LoginController->login");
$f3->route("GET @register: /register", "RegisterController->render");
$f3->route("POST @register: /register", "RegisterController->register");

// contact-us.html
$f3->route("GET @contactUs: /contact-us", "PagesController->contactUs");
// contact-us-guest.html
$f3->route("GET @contactUsGuest: /contact-us-guest", "PagesController->contactUsGuest");
 
// app.html
$f3->route("GET @app: /app", "AppController->render");
$f3->route("GET @appList: /app/list/@id", "AppController->render");
$f3->route("GET @appListMode: /app/list/@id/@mode", "AppController->render");
$f3->route("POST @createList: /app/list/create", "ListController->create");
$f3->route("POST @createTask: /app/task/create", "TaskController->create");
$f3->route("GET @toggleTask: /app/task/toggle/@id", "TaskController->toggleTask");

$f3->route("GET @logout: /logout", "PagesController->logout");
 
// Profile update and delete routes
$f3->route("GET @profile: /profile", "ProfileController->render");
$f3->route("POST @profileUpdate: /update", "ProfileController->update");
$f3->route("POST @profileDelete: /delete", "ProfileController->delete");

// Catch invalid url, redirect to home
/*$f3->route("GET /*", "LoginController->render");
$f3->route("POST /*", "LoginController->render");*/




// Start
$f3->run();