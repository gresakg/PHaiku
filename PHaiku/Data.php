<?php
namespace PHaiku;

class Data extends \stdClass {
	
	public function __get($name) {
		return "";
	}
	
	public function addChild($name) {
		$this->$name = new \PHaiku\Data();
	}
	
	public function appendArray(array $data) {
		foreach($data as $key => $item) {
			$this->$key = $item;
		}
	}
	
}

