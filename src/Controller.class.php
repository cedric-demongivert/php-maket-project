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
	
	/* ------------------------------------ */
	/*		GETTER(S) :						*/
	/* ------------------------------------ */
	public function getTitle() {
		return $this->title;	
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function getInfo() {
		return $this->info;
	}
	
	public function getMeta() {
		return $this->meta;
	}
	
	public function getImport() {
		return $this->import;
	}	
	
}

?>