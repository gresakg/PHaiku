<?php
namespace PHaiku;

class Data extends \stdClass {
	
	public function __get($name) {
		return "";
	}
	
	public function addChild($name) {
		$this->$name = new \PHaiku\Data();
	}
	
}

