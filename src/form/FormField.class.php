<?php

abstract class FormField {
	
	protected $name;
	protected $id;
	protected $type;
	protected $value;
	protected $empty;
	protected $label;
	
	public function __construct($label, $name, $id, $type, $value, $empty) {
		$this->label = $label;
		$this->name = $name;
		$this->id = $id;
		$this->type = $type;
		$this->value = $value;
		$this->empty = $empty;
	}
	
	public function canBeEmpty() {
		return $this->empty;
	}
	
	public abstract function validate();
	
	public function getHtml() {
		return "<input type='{$this->type}' name='{$this->name}' id='{$this->id}' value='$this->value />'";
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function setLabel($label) {
		$this->label = $label;
	}
	
}

?>