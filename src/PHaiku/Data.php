<?php
namespace PHaiku;

/**
 * The main purpose of the Data class is to have an extensible object oriented 
 * storage for variables.
 */
class Data extends \stdClass {
	
	/**
	 * Magic getter returns an empty string if non existing propperty gets
	 * called, to avond warnings and notices
	 * @param string $name stdClass property name
	 * @return empty string
	 */
	public function __get($name) {
		return "";
	}
	
	/**
	 * Instantiates a child stdClass tho the propperty $name
	 * @param string $name stdClass property name
	 */
	public function addChild($name) {
		$this->$name = new \PHaiku\Data();
	}
	
	/**
	 * Appends an array to the current object as key->value pairs. Existing keys
	 * are overwritten.
	 * @param array $data
	 */
	public function appendArray(array $data) {
		foreach($data as $key => $item) {
			$this->$key = $item;
		}
	}
	
}

