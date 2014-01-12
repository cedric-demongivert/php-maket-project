<?php

class Form {
	
	private $id;
	private $action;
	private $method;
	private $fields;
	private $submit;
	
	public function __construct($id, $action, $submit="envoyer", $method="post") {
		$this->id = $id;
		$this->action = $action;
		$this->method = $method;
		$this->submit = $submit;
		$this->fields = array();
	}
	
	public function check($array) {
		
		$result = array();
		
		for($fields as $field) {
			
			if(!$field->canBeEmpty() && empty($array[$field->getName()])) {
				$result[] = array(0, $field);
			}
			
			if(!$field->validate()) {
				$result[] = array(1, $field);
			}
			
		}
		
		return $result;
		
	}
	
	public function addField($field) {
		$this->fields[] = $field;
	}
	
	public function getHtmlStart() {
		return "<form id='{$this->id}' method='{$this->method}' action='{$this->action}' >";
	}
	
	public function getHtmlEnd() {
		return "</form>";
	}
	
	public function getField($i) {
		return $this->fields[$i];
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function getSubmit() {
		return $this->submit;
	}
	
	public function setSubmit($submit) {
		$this->submit = $submit;
	}
	
}

?>