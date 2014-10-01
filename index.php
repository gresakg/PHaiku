<?php
$startbench = microtime(true);

require_once __DIR__.'/vendor/autoload.php';

// define services

// use pimple as dependency injector
$di = new \Pimple\Container();

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

$di['cache'] = $di->factory(function () {
	$cache = new Gresakg\Cache\Adapter\MemCache();
	$cache->addServer("localhost", "11211");
	return new Gresakg\Cache\Cache($cache);
});

//add custom services
include __DIR__.'/config/services.php';

// end services definition

//write cache if cache enabled
if($di['config']['cache']) {
	$di['slim']->hook('slim.after', function() use ($di) {
		$di['haiku']->setCache();
	});
}
// for security reasons define stricr route conditions
\Slim\Route::setDefaultConditions($di['config']['route.conditions']);

//define the base directory as the current directory
\PHaiku\PHaiku::$basedir = __DIR__;

//define the version
\PHaiku\PHaiku::$version = "0.82.39.14";

//set the routes
\PHaiku\PHaiku::setRoutes($di);

if($di['config']['cache']) {
	$di['slim']->get("/clearcache", function() use($di) {
		$di['cache']->dropCache();
		$di['slim']->redirect("/");
	});
}
//run slim
$di['slim']->run();

if ($di['config']['benchmark']) {
	$endbench = microtime(true);
	echo "<p>Execution time: ".number_format(($endbench - $startbench), 4)."s Memory usage: ". (memory_get_peak_usage()/1000000)."MB</p>";
}