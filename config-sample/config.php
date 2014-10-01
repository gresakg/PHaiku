<?php
return [
	"mode" => "developement",
	"debug" => 'true',
	"benchmark" => false,
	"cache" => true,
	"cache.adapter" => "File",
	"cache.time" => 300,
	"templates.path" => "./templates/skela",
	"class.name" => "\PHaiku\Haiku",
	"data.store" => "./data",
	"default.ext" => "html",
	"contact.mail" => "",
	"mail.host" => "",
	"mail.username" => "",
	"mail.password" => "",
	"recaptcha.publickey" => "",
	"recaptcha.privatekey" => "",
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
			"arguments" => ["widgets/twitter"]
		],
		"discuss" => [
			"handler"=>"textWidget",
			"arguments" => ["widgets/discuss"]
		],
		"analytics" => [
			"handler"=>"textWidget",
			"arguments" => ["widgets/analytics"]
		],
		"forkme" => [
			"handler"=>"textWidget",
			"arguments" => ["widgets/forkme"]
		],
		"news" => [
			"handler"=>"newsWidget",
			"arguments"=>[5]
		],
	],
	//define basic routes
	"routes" => [
		[
			"name" => "index",
			"route" => "/",
			"method" => "get",
			"handler" => "frontPage",
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
			"route" => "/contact/",
			"method" => "get",
			"handler" => "contactForm",
		],
		[
			"name" => "contactok",
			"route" => "/contact/ok/",
			"method" => "get",
			"handler" => "contactOk",
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

