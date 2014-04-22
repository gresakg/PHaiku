<?php

namespace Haiku;

class Haiku {
	
	protected $app;
	
	protected $lang;
	
	public $lang_route = "";
	
	public $data;
	
	private static $instance;
	
	public function __construct(\Slim\Slim $app) {
		self::$instance = $this;
		$this->app = $app;
		$this->init();
		
	}
	
	public function init() {
		$this->setLangRoute();
	}
	
	public function setBasicData($lang) {		
		$filename = $this->getFilepath("_config",$lang,"php");
		if(file_exists($filename)) {
			$data = include $filename;
		}
		$data['language'] = $this->lang;
		$data['template_url'] = $this->getBaseUrl().(trim($this->app->config("templates.path"),"."));	
		return $this->data = $data;
	}
	
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
	
	public function getBaseUrl() {	
		return $this->app->env['slim.url_scheme']
			."://".$this->app->env['SERVER_NAME']
			.(empty($this->app->env['SCRIPT_NAME'])?"":$this->app->env['SCRIPT_NAME']);
	}
	
	private function getFilepath($page, $lang,$ext=false) {
		if($ext === false) $ext = $this->app->config("default.ext");
		return BASEPATH.trim($this->app->config("data.store"),".")."/".$lang."/".$page.".".$ext;
	}
	
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

