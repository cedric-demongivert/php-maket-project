<?php

abstract class Controller {
	
	/* ------------------------------------ */
	/*		ATTRIBUT(S) :					*/
	/* ------------------------------------ */
	protected $title;
	protected $error;
	protected $info;
	protected $meta;
	protected $import;
	protected $controllers;
	protected $controllerName;
	
	/* ------------------------------------ */
	/*		CONSTRUCTEUR(S) :				*/
	/* ------------------------------------ */
	public function __construct() {
		$this->title = "untitled";
		$this->error = null;
		$this->info = null;
		$this->meta = null;
		$this->import = null;
		$this->controllers = array();
		$this->controllerName = "controller";
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
	
	public function includeController($controllerName, $controller) {
		$this->controllers[$controllerName] = $controller;
	}
	
	public function getIncludedControllers() {
		return $this->controllers;
	}
	
	public function getControllerName() {
		return $this->controllerName;
	}
	
}

?>