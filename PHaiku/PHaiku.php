<?php

namespace PHaiku;

abstract class PHaiku {
	
	/**
	 * Defines absolute path
	 * @var string
	 */
	public static $basedir;
	
	/**
	 * Phaiku version.
	 * @var string 
	 */
	public static $version;
	
	/**
	 * Instance of Slim
	 * @var object
	 */
	protected $app;
	
	/**
	 * Current language
	 * @var string 
	 */
	protected $lang;
	
	/**
	 * Route prefix for language
	 * @var string /:lang or empty
	 */
	private $lang_route = "";
	
	/**
	 * Storage of variables that will be accessible in the template as keys
	 * @var array
	 */
	protected $data = [];
	
	public function __construct(\Pimple\Pimple $di) {
		$this->app = $di['slim'];
		$this->env = $di['env'];
		$this->conf = $di['config'];
		$this->data = $di['data'];
		$this->di = $di;
		$this->setLangRoute();
		
	}
	
	/**
	 * Setup routing. Define your routes in config/config.php
	 * This static method is called from the front controller (index.php)
	 * Each route when called by slim then actually instantiates Phaiku.
	 * @param \Pimple\Pimple $di
	 */
	public static function setRoutes(\Pimple\Pimple $di) {
		$routes = $di['config']["routes"];
		foreach($routes as $route) {
			$di['slim']->map( $di['haiku']->lang_route.$route['route'], function() use ($di, $route) {
				$args = $di['haiku']->init(func_get_args());
				$di['haiku']->$route['handler']($args);
			})->via(strtoupper($route['method']))->name($route['name']);
		}
	}
	
	/**
	 * Initialises the environement after the route is known
	 * This method is called from every route defined with setRoutes method.
	 * @param array $args an array of route arguments
	 * @return array $args arguments
	 */
	private function init($args) {
		$args = $this->processArgs($args);
		$this->setBasicData($this->lang);
		$this->setWidgets();
		
		return $args;
	}
		
	/**
	 * This method processes arguments passed by the route.
	 * @param array $args an array of route arguments, eventually multidimensional.
	 * @return array $args arguments
	 */
	private function processArgs($args) {
		/**
		 * In a multilingual environement, the first argument passed is allways a language.
		 * Language set by url allways have precedence, that's why we never call getLang()
		 */
		if($this->app->config("multilingual")) {
			$this->lang = array_shift($args);	
			if($this->lang != $this->app->getCookie("haikulang")) {
				$this->app->setCookie("haikulang", $this->lang);
			}
		}
		else {
			$this->lang = "";
		}
		
		return $args;
	}
	
	/**
	 * Fetches and sets data common to all the pages
	 * @return array the set data are also returned
	 */
	protected function setBasicData() {		
		$filename = $this->getFilepath("_config","php");
		if(file_exists($filename)) {
			$base = include $filename;
		}
		$this->data['site']->appendArray($base);
		$this->data['site']->baseurl = $this->getBaseUrl();
		$this->data['site']->language = empty($this->lang)?$this->app->config("default.lang"):$this->lang;
		$this->data['site']->template_url = $this->getBaseUrl().(trim($this->app->config("templates.path"),"."));
		$this->data['site']->phaiku_version = self::$version;
		//$this->data = array_merge($this->data, $data);
	}
	
	/**
	 * Sets the default widgets defined in the configuration. 
	 * @return object of type \PHaiku\Data for use in templates
	 */
	protected function setWidgets() {
		$widgets = $this->app->config("widgets");
		foreach($widgets as $name => $params) {
			$this->addWidget($name,$params);
		}
	}
	
	/**
	 * Creates a new instance of \PHaiku\Data
	 * @return object of class \PHaiku\Data
	 */
	public function newData() {
		return $this->di['newdata'];
	}
	
	/**
	 * Adds a widget to the widgets object.
	 * @param type $name
	 * @param type $params
	 */
	public function addWidget($name,$params) {	
		if(!is_array($params['arguments'])) {
			$params['arguments'] = explode(",",$params['arguments']);
		}
		$this->data["widgets"]->$name = call_user_func_array(array($this,$params['handler']), $params['arguments']);
	}
	
	/**
	 * Empties out the widgets property defined by $name, so that the widget will
	 * only print an empty string, resulting in a removal of the widget from the 
	 * rendered page.
	 * @param string $name name of the property to be emptied
	 */
	public function removeWidget($name) {
		$this->data['widgets']->$name = "";
	}
	
	/**
	 * The main page controller that calls the \Slim\View::render() function
	 * @param array $args
	 */
	public function setPage(array $args) {
		if(empty($args)) { 
			$page = "index";
		}
		else {
			$page = implode("/", $args[0]);
		}
		$page = "pages/".$page;
		$this->data['page']->content = $this->textWidget($page);
		if(empty($this->data['page']->content))
			$this->app->pass();
		$this->app->render("index.php", $this->data);
	}
	
	/**
	 * This widget handler reads the contents of a html file and returns it.
	 * @param string $name filename
	 * @return string contents of html file 
	 */
	public function textWidget($name) {
		$filename = $this->getFilepath($name);
		if(file_exists($filename)) {
			return file_get_contents($filename);
		}
		else {
			return "";
		}
	}
	
	/**
	 * PHaiku's wrapper for slims urlFor function that takes into account the lang 
	 * arguments
	 * @param string $routename the name of the route as defined in config routes
	 * @param array $args arguments to be passed to the route, language excluded
	 * @return string href link
	 */
	public function setUrl($routename, array $args=array()) {
		if($this->app->config("multilingual")) {
			$args = array_merge(["lang"=>$this->lang],$args);
		}
		return $this->app->urlFor($routename,$args);
		
	}
	
	/**
	 * Constructs the language menu according to the config(languages) setting.
	 * @return html string containing a language menu or false
	 */
	protected function langMenu() {
		$languages = $this->app->config("languages");
		if(is_array($languages) && $this->app->config('multilingual')) {
			foreach($languages as $lang) $langs[$lang] = "/".$lang;
			return self::menuIterator($langs, $this->getBaseUrl(), "lang");
		}
		else {
			return false;
		}	
	}
	
	/**
	 * Builds the main menu. Define your menu items in the widgets/menu.php file.
	 * @return html string containing menu
	 */
	protected function setMenu() {
		$filename = $this->getFilepath("widgets/menu", "php");
		if(!file_exists($filename)) return;
		$menudata = require $filename;		
		$baseurl = $this->getBaseUrl().(empty($this->lang)?"":"/".$this->lang);
		$this->data['page']->menu = self::menuIterator($menudata, $baseurl, "nav");
		$this->app->view->appendData($this->data);
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
	public function getFilepath($page, $ext=false) {
		if($ext === false) $ext = $this->app->config("default.ext");
		return self::$basedir.trim($this->app->config("data.store"),".")."/".$this->lang."/".$page.".".$ext;
	}
	
	/**
	 * Sets route prefix if the site is multilingual
	 */
	private function setLangRoute() {
		if($this->app->config('multilingual')) {
			$this->lang_route = "/:lang";
			$this->getLang();
			$self = $this;
			$this->app->get("/", function() use ($self) {
				$self->app->redirect($self->lang);
			});
		}
		else {
			$this->lang_route = "";
		}
	}
	
	/**
	 * Sets the language and returns the language and the language cookie
	 * We only need this to define language for base route on multilingual system
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
		$deflang = $this->app->config("default.language");
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
	
}