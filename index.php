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
$f3->route("POST @createList: /create-list", "AppController->createList");

$f3->route("GET @logout: /logout", "PagesController->logout");

/* Catch invalid url, redirect to home */
$f3->route("GET /*", "LoginController->render");
$f3->route("POST /*", "LoginController->render");


// Start
$f3->run();