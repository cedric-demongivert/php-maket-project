<?php

abstract class Controller {
	
	protected $bdd;

	public function __construct($bdd) {
		$this->bdd = $bdd;
	}
	
	abstract public function getData();
	
}

?>