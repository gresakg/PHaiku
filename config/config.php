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
	//setup default widgets
	"widgets" => [
		"menu" => [
			"handler"=>"setMenu",
			"arguments" => NULL
		],
		"langmenu" => [
			"handler"=>"langMenu",
			"arguments" => NULL
		],
		"haiku" => [
			"handler"=>"haikuWidget",
			"arguments" => NULL
		],
		"twitter" => [
			"handler"=>"textWidget",
			"arguments" => "twitter"
		],
		"discuss" => [
			"handler"=>"textWidget",
			"arguments" => ["discuss"]
		],
		"analytics" => [
			"handler"=>"textWidget",
			"arguments" => "analytics"
		],
		"forkme" => [
			"handler"=>"textWidget",
			"arguments" => "forkme"
		],
	],
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
			"name" => "news",
			"route" => "/news",
			"method" => "get",
			"handler" => "theNews",
		],
		[
			"name" => "newsitem",
			"route" => "/n/:segments+",
			"method" => "get",
			"handler" => "newsItem",
		],
		[
			"name" => "contact",
			"route" => "/contact",
			"method" => "get",
			"handler" => "contactForm",
		],
		[
			"name" => "postcontact",
			"route" => "/contact/:token",
			"method" => "post",
			"handler" => "contactForm",
		],
	],
	// define default route conditions
	"route.conditions" => [
		'page+' => '[a-zA-Z0-9_-]+',
		'lang' => '[\w]{2}',
	],
];

