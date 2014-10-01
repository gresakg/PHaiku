<?php
return [
	"mode" => "development",
	"debug" => true,
	"benchmark" => true,
	"cache" => true,
	"cache.adapter" => "File",
	"cache.time" => 120,
	"cache.flush.url" => "/clearcache",
	"templates.path" => "./templates/skela",
	"class.name" => "\PHaiku\Haiku",
	"data.store" => "./data",
	"default.ext" => "html",
	"contact.mail"=> "",
	"mail.host" => "",
	"mail.username" => "",
	"mail.password" => "",
	"recaptcha.publickey" => "your-recaptcha-public-key",
	"recaptcha.privatekey" => "your-recaptcha-private-key",
	"multilingual" => true,
	"default.language" => "en",
	"languages" => ["sl","en"],
];

