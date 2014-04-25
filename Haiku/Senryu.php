<?php
namespace Haiku;

class Senryu extends Haiku {
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	public function setWidgets() {
		$widgets = new \stdClass();
		$widgets->menu = $this->setMenu();
		return $widgets;
	}
	
	private function setMenu() {
		$menu = require $this->getFilepath("_menu", $this->lang, "php");
		$baseurl = $this->getBaseUrl()."/".$this->lang;
		
		$this->app->view->appendData(["menu"=>$menu,"baseurl"=>$baseurl]);
		return $this->app->view->fetch("widgets/menu.php");
	}

	
}