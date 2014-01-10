<?php

class Controller {
	
	protected $bdd;

	function __construct($bdd) {
		$this->bdd = $bdd;
	}
	
	abstract function getData();
	
}

?>