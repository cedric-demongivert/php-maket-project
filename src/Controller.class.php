<?php

abstract class Controller {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	protected $title;
	protected $error;
	protected $info;
	
	protected $controllers;
	protected $controllerName;
	protected $controllerTemplate;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct($controllerName, $controllerTemplate) {
		$this->title = "untitled";
		$this->error = null;
		$this->info = null;
		$this->controllers = array();
		$this->controllerName = $controllerName;
		$this->controllerTemplate = $controllerTemplate;
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	public function includeController($controllerName, $controller) {
		$this->controllers[$controllerName] = $controller;
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function getTitle() {
		return $this->title;	
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function getInfo() {
		return $this->info;
	}
	
	public function getIncludedControllers() {
		return $this->controllers;
	}
	
	public function getName() {
		return $this->controllerName;
	}
	
	public function getTemplate() {
		return $this->controllerTemplate;
	}
	
}

?>