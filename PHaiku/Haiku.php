<?php
namespace PHaiku;

class Haiku extends PHaiku {
	
	public $widgets;
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	public function frontPage() {
		$this->removeWidget("discuss");
		$this->addWidget("section1", ["handler"=>"textWidget","arguments"=>["front/section1"]]);
		$this->addWidget("section2", ["handler"=>"textWidget","arguments"=>["front/section2"]]);
		//var_dump($this->data['widgets']);
		$this->app->view->appendData($this->data);
		$this->data['content'] = $this->app->view->fetch("frontpage.php");
		$this->app->render("index.php", $this->data);
	}	
	
	public function theNews() {
		$this->removeWidget("discuss");
		 $this->data['content'] = $this->newsWidget(20);
		 $this->app->render("index.php", $this->data);
		
	}
	
	public function newsItem(array $args) {
		$args = $args[0];
		$filename = $this->getFilepath("news/".implode("_",$args),"php");
		if(file_exists($filename)) {
			$news = include $filename;
		}
		$news['content'] = nl2br($news['content']);
		$news['date'] = $this->getNewsDate($args);
		$this->app->view->appendData($news);
		$this->data['content'] = $this->app->view->fetch("newsitem.php");
		$this->app->render("index.php", $this->data);
		
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
	
	public function newsWidget($n) {
		$d = dir(trim($this->getFilepath("news", ""),"."));
		$content = "<h2>News</h2>";
		$i=0;
		while (false !== ($file = $d->read())) {
			$i++;
			$file = trim($file,".php");
			$meta = explode("_", $file);
			if(count($meta)<3)	continue;
			$filename = $this->getFilepath("news/".$file,"php");
			if(file_exists($filename)) {
				$news = include $filename;
			} else continue;
			$news['content'] = nl2br($news['content']);
			$news['date'] = $this->getNewsDate($meta);
			$url = implode("/", array_shift(array_chunk($meta, 3)))."/".implode("_",array_splice($meta,3));
			$news['link'] = $this->setUrl("newsitem", ["segments"=>$url]);
			
			$this->app->view->appendData($news);
			$content .= $this->app->view->fetch("widgets/news.php");
			if($i>=$n) break;
		 }
		 $d->close();
		 return $content;
	}
	
	public function haikuWidget() {
		$filename = $this->getFilepath("widgets/haikus","php");
		if(file_exists($filename)) {
			$haikus = include $filename;
		}
		return "<blockquote>".nl2br($haikus[array_rand($haikus)])."</blockquote>";
		
	}
	
	protected function getNewsDate(array $meta) {
		$time = mktime(0,0,0,(int) $meta[1], (int) $meta[2], (int) $meta[0]);
		return date("d. m. Y",$time);
	}
	
	protected function processForm() {
		//redirects on success
		//return errors and data on fail
		echo $this->app->request->post("name");
	}

	
}