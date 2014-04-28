<?php
namespace PHaiku;

class Haiku extends PHaiku {
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	/**
	 * This is the implementation of the init abstract method. You can use it to
	 * add your own initialisations.
	 */
	public function init() {
		
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
		return $widgets;
	}
	
	/**
	 * Just an example of a custom controller method, called if you access [lang]/contact
	 */	
	public function contactForm() {
	
		echo "contact form";
	}

	
}