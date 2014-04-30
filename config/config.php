<?php
return [
	"mode" => "developement",
	"debug" => 'true',
	"templates.path" => "./templates/skela",
	"class.name" => "\PHaiku\Haiku",
	"data.store" => "./data",
	"default.ext" => "html",
	"multilingual" => true,
	"default.language" => "en",
	"languages" => ["sl","en"],
	//define basic routes
	"routes" => [
		[
			"name" => "index",
			"route" => "/",
			"method" => "get",
			"handler" => "setPage",
		],
		[
			"name" => "page",
			"route" => "/p/:page+",
			"method" => "get",
			"handler" => "setPage",
		],	
	//custom routes
		[
			"name" => "contact",
			"route" => "/contact",
			"method" => "get",
			"handler" => "contactForm",
		],
	],
	// define default route conditions
	"route.conditions" => [
		'page+' => '[a-zA-Z0-9_-]+',
		'lang' => '[\w]{2}',
	],
];

