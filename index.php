<?php
// Load the composer required libraries
require "vendor/autoload.php";

// Load the framework
$f3 = Base::instance();

// Automatically load the controller classes and views
$f3->set("AUTOLOAD", "controllers/");
$f3->set("UI", "views/");

// Enable debug
// TODO: Remove for production
$f3->set("DEBUG", 3);

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
$f3->route("GET @app: /app", "PagesController->app");
$f3->route("GET @logout: /logout", "PagesController->logout");
// main.html
$f3->route("GET @main: /app", "PagesController->app");
// Start
$f3->run();