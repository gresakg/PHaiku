<?php

namespace Haiku;

class Haiku {
	
	/**
	 * instance of Slim
	 * @var object
	 */
	protected $app;
	
	/**
	 * current language
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
	
	public function __construct(\Slim\Slim $app) {
		$this->app = $app;
		$this->init();
		
	}
	
	/**
	 * Class initialization
	 */
	public function init() {
		$this->setLangRoute();
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
	 * The page controller that calls the render function
	 * @param array $args
	 */
	public function setPage($args) {
		$page = $this->processArgs($args);
		$lang = $this->app->config("multilingual")?$this->lang:"";
		$data = $this->setBasicData($lang); //TODO pass $lang so it works in non multilingual mode just like pages
		
		$filename = $this->getFilepath($page,$lang);
		if(file_exists($filename)) {
			$this->data['content'] = file_get_contents($filename);
		} else {
			$this->app->pass();
		}
		$this->app->render("index.php", $this->data);
	}
	
	/**
	 * Absolute url for the curent domain used for constructing links
	 * @return string url
	 */
	public function getBaseUrl() {	
		return $this->app->env['slim.url_scheme']
			."://".$this->app->env['SERVER_NAME']
			.(empty($this->app->env['SCRIPT_NAME'])?"":$this->app->env['SCRIPT_NAME']);
	}
	
	/**
	 * Gets absolute path of the data file
	 * @param string $page the requested file
	 * @param string $lang language of the requested file
	 * @param string $ext extension of the requested file
 	 * @return string absolute path to the data file
	 */
	private function getFilepath($page, $lang,$ext=false) {
		if($ext === false) $ext = $this->app->config("default.ext");
		return BASEPATH.trim($this->app->config("data.store"),".")."/".$lang."/".$page.".".$ext;
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
	 * @return type
	 */
	private function getLang() {
		if($this->app->config('multilingual')) {
			$this->lang = $this->app->getCookie('haikulang');
			
		}
		
		if(empty($this->lang)) {
				$this->lang = $this->app->config("default.language");
		}
		
		$this->app->setCookie('haikulang',$this->lang,'2 days');
		
		return $this->lang;
		
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

