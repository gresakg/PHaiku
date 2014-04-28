<?php
$startbench = microtime(true);

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

// end services definition

\Slim\Route::setDefaultConditions($di['config']['route.conditions']);

\PHaiku\PHaiku::$basedir = __DIR__;

\PHaiku\PHaiku::setRoutes($di);


$di['slim']->run();
$endbench = microtime(true);
echo "<p>Execution time: ".number_format(($endbench - $startbench), 4)."s Memory usage: ". (memory_get_peak_usage()/1000000)."MB</p>";