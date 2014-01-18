<?php

abstract class Controller {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	protected $title;
	protected $info;
	protected $error;
	
	protected $controllers;
	protected $controllerName;
	protected $controllerTemplate;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct($controllerName, $controllerTemplate) {
		$this->title = "untitled";
		
		if(isset($_SESSION['error'])) {
			$this->error = $_SESSION['error'];
		}

		if(isset($_SESSION['info'])) {
			$this->info = $_SESSION['info'];
		}
		
		$_SESSION['error'] = null;
		$_SESSION['info'] = null;
		
		$this->controllers = array();
		$this->controllerName = $controllerName;
		$this->controllerTemplate = $controllerTemplate;
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	public function includeController($controller) {
		$this->controllers[$controller->getName()] = $controller;
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function getTitle() {
		return $this->title;	
	}
	
	public function setError($error) {
		$_SESSION['error'] = $error;
		$this->error = $error;
	}
	
	public function addError($error) {
		$_SESSION['error'] .= $error;
		$this->error .= $error;
	}
	
	public function setInfo($info) {
		$_SESSION['info'] = $info;
		$this->info = $info;
	}
	
	public function addInfo($info) {
		$_SESSION['info'] .= $info;
		$this->info .= $info;
	}
	
	public function getError() {
		$_SESSION['error'] = null;
		return $this->error;
	}
	
	public function getInfo() {
		$_SESSION['info'] = null;
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