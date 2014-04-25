<?php
namespace Haiku;

class Senryu extends Haiku {
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	public function init() {
		
	}
	
	public function setWidgets() {
		$widgets = new \stdClass();
		$widgets->menu = $this->setMenu();
		return $widgets;
	}
	
	private function setMenu() {
		$filename = $this->getFilepath("_menu", $this->lang, "php");
		if(!file_exists($filename)) return;
		$menu = require $filename;
		
		$baseurl = $this->getBaseUrl()."/".$this->lang;
		
		$this->app->view->appendData(["menu"=>$menu,"baseurl"=>$baseurl]);
		return $this->app->view->fetch("widgets/menu.php");
	}

	
}