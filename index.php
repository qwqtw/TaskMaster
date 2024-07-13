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
$f3->route("GET @home: /", "PagesController->login");
$f3->route("GET @signUp: /sign-up", "PagesController->signUp");

$f3->route("GET @contactUs: /contact-us", "PagesController->contactUs");




// Start
$f3->run();
