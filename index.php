<?php
$startbench = microtime(true);

require_once __DIR__.'/vendor/autoload.php';

// define services

// use pimple as dependency injector
$di = new \Pimple\Container();

$default = require __DIR__.'/src/config-dist.php';
$config = require __DIR__.'/config/config.php';
$routes = require __DIR__.'/config/routes.php';
$di['config'] = array_merge(array_merge($default, $config),$routes);

$di['cache'] = $di->factory(function () use ($di) {
	$adapter = "Gresakg\Cache\Adapter\\".$di['config']['cache.adapter'];
	$cache = new $adapter();
	return new Gresakg\Cache\Cache($cache);
});

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

//write cache if cache enabled
if($di['config']['cache']) {
	$di['slim']->hook('slim.after', function() use ($di) {
		if($di['slim']->response->isOk())
			$di['haiku']->setCache();
	});
}
// for security reasons define stricr route conditions
\Slim\Route::setDefaultConditions($di['config']['route.conditions']);

//define the base directory as the current directory
\PHaiku\PHaiku::$basedir = __DIR__;

//define the version
\PHaiku\PHaiku::$version = "0.85.40.14";

//set the routes
\PHaiku\PHaiku::setRoutes($di);

//route for clearing the cache
if($di['config']['cache']) {
	$di['slim']->get($di['config']['cache.flush.url'], function() use($di) {
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