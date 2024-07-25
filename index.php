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
$f3->route("POST @home", "LoginController->login");
$f3->route("GET @register: /register", "RegisterController->render");
$f3->route("POST @register", "RegisterController->register");

// contact-us.html
$f3->route("GET @contactUs: /contact-us", "PagesController->contactUs");
 
// app.html
$f3->route("GET @app: /app", "AppController->render");

$f3->route("GET @appList: /app/list/@id", "AppController->setList");
$f3->route("DELETE @deleteList: /app/list/@id/delete", "ListController->delete");
$f3->route("GET @updateListOrder: /app/list/@id/@order", "ListController->updateListOrder");
$f3->route("GET @appListMode: /app/list/mode/@mode", "AppController->setMode");
$f3->route("GET @appListPriority: /app/list/priority/toggle", "AppController->setByPriority");
$f3->route("GET @appListDueDate: /app/list/dueDate", "AppController->setByDueDate");

$f3->route("POST @createList: /app/list/create", "ListController->create");
$f3->route("POST @editListTitle: /app/list/editTitle", "ListController->editTitle");

// Tasks
$f3->route("GET @baseTask: /app/task", null);
$f3->route("POST @baseTask", "TaskController->createTask");

$f3->route("GET @getTask: /app/task/@id", "TaskController->getTask");
$f3->route("POST @getTask", "TaskController->updateTask");
$f3->route("DELETE @getTask", "TaskController->deleteTask");
$f3->route("GET @toggleTask: /app/task/@id/toggle", "TaskController->toggleTask");
 
// Profile update and delete routes
$f3->route("GET @profile: /profile", "ProfileController->render");
$f3->route("POST @profileUpdate: /update", "ProfileController->update");
$f3->route("GET @profileDelete: /delete", "ProfileController->delete");

$f3->route("GET @logout: /logout", "Controller->logout");

// Catch invalid url, redirect to home
/*$f3->route("GET /*", "LoginController->render");
$f3->route("POST /*", "LoginController->render");*/




// Start
$f3->run();