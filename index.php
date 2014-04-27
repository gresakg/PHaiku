<?php

//$startbench = microtime(true);
define("BASEPATH",__DIR__);

/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require BASEPATH.'/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$config = require BASEPATH.'/config/config.php';
/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim($config);

$app->env = $app->environment();

$haiku = new $config['class.name']($app);

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

\Slim\Route::setDefaultConditions(array(
    'page+' => '[a-zA-Z0-9_-]+',
	'lang' => '[\w]{2}'
));

// GET route
$app->get(
    $haiku->lang_route.'/', 
    function () use ($haiku) {
        $haiku->setPage(func_get_args());
    }
);

// GET route
$app->get(
    $haiku->lang_route.'/p/:page+',
    function () use ($haiku) {
        $haiku->setPage(func_get_args());
    }
);

$routes = $app->config("routes");

foreach($routes as $route) {
	$app->map( $haiku->lang_route.$route['route'], function() use ($haiku, $route) {
		$haiku->$route['handler'](func_get_args());
	})->via(strtoupper($route['method']));
}



/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
//$endbench = microtime(true);
//echo "<p>Execution time: ".number_format(($endbench - $startbench), 4)."s Memory usage: ". (memory_get_peak_usage()/1000000)."MB</p>";