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
		$widgets->twitter = $this->textWidget("twitter");
		$widgets->discuss = $this->textWidget("discuss");
		$widgets->analytics = $this->textWidget("analytics");
		$widgets->forkme = $this->textWidget("forkme");
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
	
	public function textWidget($name) {
		$filename = $this->getFilepath("_".$name, $this->lang, "php");
		if(file_exists($filename)) {
			return file_get_contents($filename);
		}
		else {
			return "";
		}
	}

	
}