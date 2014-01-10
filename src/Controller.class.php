<?php

abstract class Controller {
	
	/* ------------------------------------ */
	/*		ATTRIBUT(S) :					*/
	/* ------------------------------------ */
	protected $bdd;
	protected $title;
	protected $error;
	protected $info;
	protected $meta;
	protected $import;
	
	/* ------------------------------------ */
	/*		CONSTRUCTEUR(S) :				*/
	/* ------------------------------------ */
	public function __construct($bdd) {
		$this->bdd = $bdd;
		$this->title = "untitled";
		$this->error = null;
		$this->info = null;
		$this->meta = null;
		$this->import = null;
	}
	
	/* ------------------------------------ */
	/*		METHODE(S) :					*/
	/* ------------------------------------ */
	public abstract function init();
	
}

?>