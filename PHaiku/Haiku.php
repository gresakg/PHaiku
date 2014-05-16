<?php
namespace PHaiku;

class Haiku extends PHaiku {
	
	public $widgets;
	
	public function __construct($app) {
		parent::__construct($app);
	}
	
	/**
	 * The front page handler. Renders the front page.
	 * @todo dynamic inclusion of sections
	 */
	public function frontPage() {
		$this->removeWidget("discuss");
		$this->addWidget("section1", ["handler"=>"textWidget","arguments"=>["front/section1"]]);
		$this->addWidget("section2", ["handler"=>"textWidget","arguments"=>["front/section2"]]);
		//var_dump($this->data['widgets']);
		$this->app->view->appendData($this->data);
		$this->data['page']->content = $this->app->view->fetch("frontpage.php");
		$this->app->render("index.php", $this->data);
	}	
	
	/**
	 * The news handler. Renders an agregation of news.
	 * @todo pagination options
	 */
	public function theNews() {
		$this->removeWidget("discuss");
		 $this->data['page']->content = $this->newsWidget(20);
		 $this->app->render("index.php", $this->data);
		
	}
	
	/**
	 * News item handler. Renders a news item according to the passed uri
	 * @param array $args uri arguments
	 */
	public function newsItem(array $args) {
		$args = $args[0];
		$filename = $this->getFilepath("news/".implode("_",$args),"php");
		if(file_exists($filename)) {
			$newsdata = include $filename;
		}
		$news = $this->newData();
		$news->appendArray($newsdata);
		$news->date = $this->getNewsDate($args);
		$this->app->view->appendData(["news"=>$news]);
		$this->data['page']->content = $this->app->view->fetch("newsitem.php");
		$this->app->render("index.php", $this->data);
		
	}
	
	/**
	 * @todo Contact Form handler
	 */	
	public function contactForm($args) {
		include_once self::$basedir.'/libs/recaptchalib.php';
		$this->getFormData();
		if($this->app->request->isPost())
			$this->processForm($args[0]);
		
		$this->data['form']->captcha = recaptcha_get_html($this->app->config("recaptcha.publickey"));
		$this->removeWidget("discuss");
		$token = uniqid();
		$this->data['form']->action = $this->setUrl("postcontact",array("token"=>$token)); //TODO set token
		$this->app->view->appendData($this->data);
		$this->data['page']->content = $this->app->view->fetch("contactform.php");
		$this->app->render("index.php", $this->data);
		
	}
	
	public function contactOk() {
		$this->removeWidget("discuss");
		$this->getFormData();
		$this->app->view->appendData($this->data);
		$this->data['page']->content = $this->app->view->fetch("contactok.php");
		$this->app->render("index.php", $this->data);
	}
	
	/**
	 * News widget returns a view of agregated news
	 * @param type $n limit of news to be agragated
	 * @return html string, an agregation of $n news
	 */
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
			$news = $this->newData();
			if(file_exists($filename)) {
				$newsdata = include $filename;
				$news->appendArray($newsdata);
			} else continue;
			$news->date = $this->getNewsDate($meta);
			$url = implode("/", array_shift(array_chunk($meta, 3)))."/".implode("_",array_splice($meta,3));
			$news->link = $this->setUrl("newsitem", ["segments"=>$url]);
			
			$this->app->view->appendData(["news"=>$news]);
			$content .= $this->app->view->fetch("widgets/news.php");
			unset($news);
			if($i>=$n) break;
		 }
		 $d->close();
		 return $content;
	}
	
	/**
	 * Sample widget that displays a random haiku from the widgets/haikus.php file
	 * @return html string
	 */
	public function haikuWidget() {
		$filename = $this->getFilepath("widgets/haikus","php");
		if(file_exists($filename)) {
			$haikus = include $filename;
		}
		return "<blockquote>".nl2br($haikus[array_rand($haikus)])."</blockquote>";
		
	}
	
	/**
	 * Transforms the array [Year, day, month] to a date
	 * @todo configurable date format
	 * @param array $meta
	 * @return string formated date
	 */
	protected function getNewsDate(array $meta) {
		$time = mktime(0,0,0,(int) $meta[1], (int) $meta[2], (int) $meta[0]);
		return date("d. m. Y",$time);
	}
	
	/**
	 * @todo form to be processed
	 */
	protected function processForm() {
		$errors = array();
		$name = htmlspecialchars(strip_tags(trim($this->app->request->post("name"))),ENT_QUOTES, "UTF-8");
		$eadr = trim($this->app->request->post("eadr"));
		$message = htmlspecialchars(strip_tags(trim($this->app->request->post("message"))),ENT_QUOTES, "UTF-8");
		if(preg_match("/^[\pL\s]+$/u",$name) != 1) $errors['name'] = $this->data['form']->error['name'];
		if(filter_var($eadr,FILTER_VALIDATE_EMAIL)===false) $errors['eadr'] = $this->data['form']->error['eadr'];
		if(empty($message)) $errors['message'] = $this->data['form']->error['message'];
		$resp = recaptcha_check_answer(
			$this->app->config("recaptcha.privatekey"), 
			$this->app->request->getIp(), 
			$this->app->request->post("recaptcha_challenge_field"), 
			$this->app->request->post("recaptcha_response_field"));
		if(!$resp->is_valid) $errors['recaptcha'] = $this->data['form']->error['recaptcha'];
		
		if(!empty($errors)) {	
			$this->data['errors']->appendArray($errors);
			$this->data['form']->name = $name;
			$this->data['form']->eadr = $eadr;
			$this->data['form']->message = 	$message;
		}
		else {
			$filename = $this->getFilepath("contacts","csv");
			$h = fopen($filename, "a");
			$line = '"'.$name.'","'.$eadr.'","'.addslashes($message).'";'."\n";
			fwrite($h,$line);
			fclose($h);
			
			require_once 'Mail.php';
			$from = $name. "<".$eadr.">";
			$to = $this->app->config("contact.mail");
			$subject = $this->data['form']->email_subject.$name;
			$body = $message;
			$host = $this->app->config("mail.host");
			$username = $this->app->config("mail.username");
			$password = $this->app->config("mail.password");
			
			$headers = array ('From' => $from,
				'To' => $to,
				'Subject' => $subject);
			$smtp = \Mail::factory('smtp',
				array ('host' => $host,
				  'auth' => true,
				  'username' => $username,
				  'password' => $password));

			$mail = $smtp->send($to, $headers, $body);
			
			$this->app->redirect($this->setUrl("contactok"));
		}
	}
	
	protected function getFormData() {
		$this->data['form'] = $this->newData();
		$this->data['errors'] = $this->newData();
		//need validation strings, so load early
		$filename = $this->getFilepath("widgets/form","php");
		if(file_exists($filename)) {
			$form = include $filename;
		}
		$this->data['form']->appendArray($form);
	}

	
}