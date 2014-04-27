<?php
return [
	"mode" => "developement",
	"debug" => 'true',
	"templates.path" => "./templates/skela",
	"class.name" => "\Haiku\Senryu",
	"data.store" => "./data",
	"default.ext" => "html",
	"multilingual" => true,
	"default.language" => "en",
	"languages" => ["sl","en"],
	//define your custom routes
	"routes" => [
		[
			"name" => "contact",
			"route" => "/contact",
			"method" => "get",
			"handler" => "contactForm",
		],
	],
];

