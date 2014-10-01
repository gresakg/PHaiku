<?php

return [
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
			"route" => "/contact",
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