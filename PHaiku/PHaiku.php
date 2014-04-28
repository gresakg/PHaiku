<?php

namespace PHaiku;

abstract class PHaiku {
	
	/**
	 * Defines absolute path
	 * @var string
	 */
	public static $basedir;
	
	/**
	 * Instance of Slim
	 * @var object
	 */
	public $app;
	
	/**
	 * Current language
	 * @var string 
	 */
	protected $lang;
	
	/**
	 * Route prefix for language
	 * @var string /:lang or empty
	 */
	public $lang_route = "";
	
	/**
	 * Storage of variables that will be accessible in the template as keys
	 * @var array
	 */
	public $data;
	
	public function __construct(\Pimple\Pimple $di) {
		$this->app = $di['slim'];
		$this->env = $di['env'];
		$this->conf = $di['config'];
		$this->setLangRoute();
		$this->init();
		
	}
	
	/**
	 * Add additional initializations here
	 */
	abstract public function init();
	
	/**
	 * Setup your widgets
	 * @return object of type \StdClass for use in templates
	 */
	public abstract function setWidgets();
	
	/**
	 * Setup routing. Define your routes in config/config.php
	 * @param \Pimple\Pimple $di
	 */
	public static function setRoutes(\Pimple\Pimple $di) {
		$routes = $di['config']["routes"];
		foreach($routes as $route) {
			$di['slim']->map( $di['haiku']->lang_route.$route['route'], function() use ($di, $route) {
				$di['haiku']->$route['handler'](func_get_args());
			})->via(strtoupper($route['method']))->name($route['name']);
		}
	}
	
	/**
	 * Fetches and sets data common to all the pages
	 * @param string $lang current language
	 * @return array the set data are also returned
	 */
	public function setBasicData($lang) {		
		$filename = $this->getFilepath("_config",$lang,"php");
		if(file_exists($filename)) {
			$data = include $filename;
		}
		$data['language'] = $this->lang;
		$data['template_url'] = $this->getBaseUrl().(trim($this->app->config("templates.path"),"."));	
		return $this->data = $data;
	}
	
	/**
	 * The main page controller that calls the render function
	 * @param array $args
	 */
	public function setPage(array $args) {
		$page = $this->processArgs($args);
		$lang = $this->app->config("multilingual")?$this->lang:"";
		$this->data = $this->setBasicData($lang); 
		$this->data['widgets'] = $this->setWidgets();
		$filename = $this->getFilepath($page,$lang);
		if(file_exists($filename)) {
			$this->data['content'] = file_get_contents($filename);
		} else {
			$this->app->pass();
		}
		$this->app->render("index.php", $this->data);
	}
	
	/**
	 * The language menu
	 * @param array $languages available languages
	 * @return html string containing a language menu or false
	 */
	protected function langMenu(array $languages) {
		if(is_array($languages)) {
			foreach($languages as $lang) $langs[$lang] = "/".$lang;
			return self::menuIterator($langs, $this->getBaseUrl(), "lang");
		}
		else {
			return false;
		}	
	}
	
	/**
	 * Builds the main menu
	 * @return html string containing menu
	 */
	protected function setMenu() {
		$filename = $this->getFilepath("_menu", $this->lang, "php");
		if(!file_exists($filename)) return;
		$menudata = require $filename;		
		$baseurl = $this->getBaseUrl()."/".$this->lang;
		$menu = self::menuIterator($menudata, $baseurl, "nav");
		$this->app->view->appendData(["menu"=>$menu]);
		return $this->app->view->fetch("widgets/menu.php");
	}
	
	/**
	 * The function that iterates through the menu array and builds the menu.
	 * @param array $menu menu data
	 * @param string $baseurl
	 * @param string $class css class for the ul element
	 * @return html string containing the menu
	 */
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
	
	/**
	 * Absolute url for the curent domain used for constructing links
	 * @return string url
	 */
	public function getBaseUrl() {	
		return $this->env['slim.url_scheme']
			."://".$this->env['SERVER_NAME']
			.(empty($this->env['SCRIPT_NAME'])?"":$this->env['SCRIPT_NAME']);
	}
	
	/**
	 * Gets absolute path of the data file
	 * @param string $page the requested file
	 * @param string $lang language of the requested file
	 * @param string $ext extension of the requested file
 	 * @return string absolute path to the data file
	 */
	public function getFilepath($page, $lang,$ext=false) {
		if($ext === false) $ext = $this->app->config("default.ext");
		return self::$basedir.trim($this->app->config("data.store"),".")."/".$lang."/".$page.".".$ext;
	}
	
	/**
	 * Sets route prefix if the site is multilingual
	 */
	private function setLangRoute() {
		if($this->app->config('multilingual')) {
			$this->lang_route = "/:lang";
			$self = $this;
			$this->app->get("/", function() use ($self) {
				$self->app->redirect($self->getLang());
			});
		}
		else {
			$this->lang_route = "";
		}
	}
	
	/**
	 * Sets the language and returns the language and the language cookie
	 * @return string language code
	 */
	private function getLang() {
		if($this->app->config('multilingual')) {
			$this->lang = $this->app->getCookie('haikulang');
			
		}
		
		if(empty($this->lang)) {
				$this->lang = $this->getBrowserLanguage();
		}
		
		if(empty($this->lang)) {
				$this->lang = $this->app->config("default.language");
		}
		
		$this->app->setCookie('haikulang',$this->lang,'2 days');
		
		return $this->lang;
		
	}
	
	/**
	 * Returns the browsers prefered language match
	 * @return string language code
	 */
	private function getBrowserLanguage() {
		if(!is_array($this->app->config('languages')))
			return false;
		$http_accept_language = $this->app->request->headers->get('HTTP_ACCEPT_LANGUAGE');
		$langs = explode(",",$http_accept_language);
		foreach($langs as $lang) {
			if(preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i",$lang,$matches)) {
				$language[$matches[1]] = (float)$matches[2];
			}
			else
				$language[$lang] = 1.0;
		}
		$qval = 0.0;
		foreach ($language as $key => $value) {
			$key = substr($key,0,2);	
			if(!in_array($key,$this->conf['languages'])) continue;
	
			if ($value > $qval) {
			  $qval = (float)$value;
			  $deflang = $key;
		    }
		}		
		return $deflang;
	}
	
	/**
	 * Processes arguments for the page route
	 * Sets the language in case of multilingual url and gets the uri for the requested
	 * page
	 * @param array $args
	 * @return string uri of the requested page
	 */
	private function processArgs($args) {
		if($this->app->config("multilingual")) {
			$this->lang = array_shift($args);	
			if($this->lang != $this->app->getCookie("haikulang")) {
				$this->app->setCookie("haikulang", $this->lang);
			}
		}
		else {
			$this->lang = $this->app->config("default.language");
		}
		
		$args = empty($args)?false:$args[0];
		
		if(empty($args)) 
			return "index";
		else 
			return implode("/",$args);
	}
	
}

