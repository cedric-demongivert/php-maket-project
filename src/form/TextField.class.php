<?php

class TextField extends FormField {
	
	public function __construct($name, $empty, $value="") {
		parent::__construct($name, $name, "text", $value, $empty);
	}
	
	public function validate() {
		return true;
	}
	
}

?>