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
	
	public function removeWidget($name) {
		if(isset($widget->$name)) {
			$widgets->$name = false;
		} 
		else {
			return;
		}
	}
	
	/**
	 * Just an example of a custom controller method, called if you access [lang]/contact
	 */	
	public function contactForm(array $args) {
		$uri = $this->processArgs($args);
		$this->removeWidget("discuss");
		if(!empty($uri)) {
			$formdata = $this->processForm();
		}
		$filename = $this->getFilepath("_form",$this->lang,"php");
		if(file_exists($filename)) {
			$form = include $filename;
		}
		$form['action'] = $this->app->urlFor("postcontact",array("lang"=>$this->lang, "token"=>"xxx")); //TODO set token
		$this->app->view->appendData($form);
		$this->data['content'] = $this->app->view->fetch("contactform.php");
		$this->app->render("index.php", $this->data);
		
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
	
	protected function processForm() {
		//redirects on success
		//return errors and data on fail
		echo $this->app->request->post("name");
	}

	
}