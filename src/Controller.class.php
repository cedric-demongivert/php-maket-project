<?php

class Controller {
	
	protected $bdd;

	public function __construct($bdd) {
		$this->bdd = $bdd;
	}
	
	public abstract function getData();
	
}

?>