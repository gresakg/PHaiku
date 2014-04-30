<?php
namespace PHaiku;

class Haiku extends PHaiku {
	
	public $widgets;
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	/**
	 * This is the implementation of the init abstract method. You can use it to
	 * add your own initialisations.
	 * @param object \Pimple\Pimple $di Description
	 */
	public function init(\Pimple\Pimple $di) {
		
	}
	
	/**
	 * Here you can set as many widgets as you wish. Just define the methods that
	 * build widgets and add them here. 
	 * @return \stdClass object containing widgets code to use in templates
	 */
	public function setWidgets() {
		$widgets = new \stdClass();
		$widgets->menu = $this->setMenu();
		$widgets->langmenu = $this->langMenu($this->app->config("languages"));
		$widgets->haiku = $this->haikuWidget();
		$widgets->twitter = $this->twitterWidget();
		return $widgets;
	}
	
	public function addWidget() {
		
	}
	
	/**
	 * Just an example of a custom controller method, called if you access [lang]/contact
	 */	
	public function contactForm() {
	
		echo "contact form";
	}
	
	public function haikuWidget() {
		$filename = $this->getFilepath("_haikus",$this->lang,"php");
		if(file_exists($filename)) {
			$haikus = include $filename;
		}
		return "<blockquote>".nl2br($haikus[array_rand($haikus)])."</blockquote>";
		
	}
	
	public function twitterWidget() {
		return '<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/gresakg" data-widget-id="461454369859174400">Tweets by @gresakg</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	}

	
}