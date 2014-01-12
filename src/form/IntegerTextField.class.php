<?php

class IntegerTextField extends TextFields {
	
	public function __construct($name, $empty, $value="") {
		parent::__construct($name, $empty, $value);
	}
	
	public function validate() {
		return preg_match($this->value, "/((-|+)?[0-9][0-9]+)?/") === 0;
	}
	
}

?>