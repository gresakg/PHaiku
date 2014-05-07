<?php
namespace PHaiku;

class Haiku extends PHaiku {
	
	public $widgets;
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	/**
	 * Just an example of a custom controller method, called if you access [lang]/contact
	 */	
	public function contactForm($args) {
		$this->removeWidget("discuss");
		$filename = $this->getFilepath("_form","php");
		if(file_exists($filename)) {
			$form = include $filename;
		}
		$form['action'] = $this->setUrl("postcontact",array("token"=>"xxx")); //TODO set token
		$this->app->view->appendData($form);
		$this->data['content'] = $this->app->view->fetch("contactform.php");
		$this->app->render("index.php", $this->data);
		
	}
	
	public function haikuWidget() {
		$filename = $this->getFilepath("_haikus","php");
		if(file_exists($filename)) {
			$haikus = include $filename;
		}
		return "<blockquote>".nl2br($haikus[array_rand($haikus)])."</blockquote>";
		
	}
	
	public function textWidget($name) {
		$filename = $this->getFilepath("_".$name, "php");
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