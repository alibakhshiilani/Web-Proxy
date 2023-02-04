<?php
session_start();
define("ROOT_DIR",__DIR__);
require 'vendor/autoload.php';
/*
 * Require All File With Composer
 */
error_reporting(E_ALL);
set_error_handler('\Alibakhshiilani\WebProxy\Core\Error::errorHandler');
set_exception_handler('\Alibakhshiilani\WebProxy\Core\Error::exceptionHandler');

/*
 * Create Routing Object
 */

$route = new \Alibakhshiilani\WebProxy\Core\Router();

/*
 * Add Uri To Routing System
 */

$route->add('/browse','BrowseController@browse');
$route->add('/','BrowseController@page');

/*
 * Dispatch Current Address
 */
$route->dispatch($_SERVER['REQUEST_URI']);
