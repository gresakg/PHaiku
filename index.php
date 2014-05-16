<?php
//$startbench = microtime(true);

require __DIR__.'/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

// define services

$di = new \Pimple\Pimple();

$di['config'] = require __DIR__.'/config/config.php';

$di['slim'] = function($c) {
	return new \Slim\Slim($c['config']);
};

$di['env'] = function($c) {
	return $c['slim']->environment();
};

$di['haiku'] = function($c) {
	return new $c['config']['class.name']($c);
};

$di['data'] = function() {
	$data['site'] = new \PHaiku\Data();
	$data['page'] = new \PHaiku\Data();
	$data['widgets']= new \PHaiku\Data();
	return $data;
};

$di['newdata'] = $di->factory(function () {
    return new \PHaiku\Data();
});

include __DIR__.'/config/services.php';

// end services definition

\Slim\Route::setDefaultConditions($di['config']['route.conditions']);

\PHaiku\PHaiku::$basedir = __DIR__;

\PHaiku\PHaiku::$version = "0.61.20.14";

\PHaiku\PHaiku::setRoutes($di);


$di['slim']->run();
//$endbench = microtime(true);
//echo "<p>Execution time: ".number_format(($endbench - $startbench), 4)."s Memory usage: ". (memory_get_peak_usage()/1000000)."MB</p>";