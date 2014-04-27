<?php
namespace Haiku;

class Senryu extends Haiku {
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	public function init() {
		
	}
	
	public function contactForm() {
		echo "contact form";
	}
	
	public function setWidgets() {
		$widgets = new \stdClass();
		$widgets->menu = $this->setMenu();
		$widgets->langmenu = $this->langMenu($this->app->config("languages"));
		return $widgets;
	}
	
	protected function setMenu() {
		$filename = $this->getFilepath("_menu", $this->lang, "php");
		if(!file_exists($filename)) return;
		$menu = require $filename;
		
		$baseurl = $this->getBaseUrl()."/".$this->lang;
		
		$this->app->view->appendData(["menu"=>$menu,"baseurl"=>$baseurl]);
		return $this->app->view->fetch("widgets/menu.php");
	}
	
	protected function langMenu($languages) {
		if(is_array($languages)) {
			foreach($languages as $lang) $langs[$lang] = "/".$lang;
			return self::menuIterator($langs, $this->getBaseUrl(), "lang");
		}
		else {
			return false;
		}
		
	}
	
	public static function menuIterator($menu, $baseurl, $class=false) {
		$html = "<ul".(($class!==false)?" class=\"$class\"":"").">";
		if(is_array($menu)) {
			foreach($menu as $key => $value) {
				$html .= "<li>";
				if(is_array($value)) {
					$html .= "<a href=\"#\">$key</a>";
					$html .= self::menuIterator($value, $baseurl);
					$html .= "</li>";
				}
				else {
					$html .= "<a href=\"{$baseurl}{$value}\">$key</a></li>";
				}
			}
		}
		$html .= "</ul>";
		return $html;
	}

	
}