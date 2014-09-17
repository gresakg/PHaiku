<?php
//$startbench = microtime(true);

require __DIR__.'/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

// define services

// use pimple as dependency injector
$di = new \Pimple\Pimple();

// load basic configurations
$di['config'] = require __DIR__.'/config/config.php';

// load slim as main framework
$di['slim'] = function($c) {
	return new \Slim\Slim($c['config']);
};

// set the environement variables
$di['env'] = function($c) {
	return $c['slim']->environment();
};

// load the phaiku class
$di['haiku'] = function($c) {
	return new $c['config']['class.name']($c);
};

// storages for variables that will be used in templates
// all are stdClass objects that can have children
$di['data'] = function() {
	$data['site'] = new \PHaiku\Data();
	$data['page'] = new \PHaiku\Data();
	$data['widgets']= new \PHaiku\Data();
	return $data;
};

// use the Data class to create other variables if needed
$di['newdata'] = $di->factory(function () {
    return new \PHaiku\Data();
});

//add custom services
include __DIR__.'/config/services.php';

// end services definition

// for security reasons define stricr route conditions
\Slim\Route::setDefaultConditions($di['config']['route.conditions']);

//define the base directory as the current directory
\PHaiku\PHaiku::$basedir = __DIR__;

//define the version
\PHaiku\PHaiku::$version = "0.64.38.14";

//set the routes
\PHaiku\PHaiku::setRoutes($di);

//run slim
$di['slim']->run();
//$endbench = microtime(true);
//echo "<p>Execution time: ".number_format(($endbench - $startbench), 4)."s Memory usage: ". (memory_get_peak_usage()/1000000)."MB</p>";