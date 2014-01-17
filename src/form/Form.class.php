<?php

class Form {

	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	private $checks;
	private $values;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		$this->checks = array();
		$this->values = array();
	}
		
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/**
	 * 
	 * Ajouter un champ
	 * @param String $name attribut name du champ
	 * 
	 */
	public function addField($name) {
		$this->values[$name] = null;
	}
	
	/**
	 * 
	 * Ajouter une contrainte
	 * @param String $name attribut name du champ
	 * @param FormCheck $check contrainte
	 * 
	 */
	public function addCheck($name, $check) {
		$this->checks[$name][] = $check;
	}
	
	/**
	 * 
	 * Set les valeurs des différents champs à partir d'un tableau name => value
	 * @param Array $post
	 * 
	 */
	public function complete($post) {
		
		foreach($post as $key => $value) {
			
			if(array_key_exists($key, $this->checks)) {
				$this->setValue($key, $value);
			}
			
		}
		
	}
	
	/**
	 * 
	 * Vérifie un ensemble de valeurs
	 * @param Array $post valeurs : name => value
	 * 
	 * @return true si le tableau est valide, Array("name" => Array(Array("check"=>FormCheck,"error" =>String))) sinon
	 * 
	 */
	public function evaluate($post) {
		
		$errors = array();
		
		foreach($this->checks as $key => $checks) {
			
			$value = null;
			if(array_key_exists($key, $post)) {
				$value = $post[$key];
			}
			
			foreach($this->checks[$key] as $check) {
				
				if(!$check->check($value)) {
					$errors[$key][] = array("check" => $check, "error" => $check->errorMessage());
				}
					
			}
			
		}
			
		if(empty($errors)) {
			return true;
		}
		else {
			return $errors;
		}
		
	}
	
	public static function toStringErrors($errors) {
		$singleton = false;
		$errorMsg = "";
		foreach($errors as $label => $subErrors) {
			foreach($subErrors as $error) {
				if($singleton) {
					$errorMsg .="<br/>";
				}
				else {
					$singleton = true;
				}
				$errorMsg .= "$label : {$error['error']}";
			}
		}
		
		return $errorMsg;
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	/**
	 * 
	 * Récupérer une valeur ajoutée au formulaire
	 * @param String $name, nom du champ
	 * @param boolean $html, si sortie html ou non (défaut vrai)
	 * 
	 * @return String valeur du champ
	 * 
	 */
	public function getValue($name, $html = true) {
		if(array_key_exists($name, $this->values)) {
			if($html) {
				return htmlentities($this->values[$name]);
			}
			else {
				return $this->values[$name];
			}
		}
		else {
			$trace = debug_backtrace();
			trigger_error(
			htmlentities(
				"Impossible de récupérer la valeur $name, celle-ci n'existe pas " .
				' dans ' . $trace[0]['file'] .
				' à la ligne ' . $trace[0]['line']),
				E_USER_ERROR);
		}
	}
	
	/**
	 * 
	 * Mettre à jour une valeur du formulaire
	 * @param String $name
	 * @param String $value
	 * 
	 */
	public function setValue($name, $value) {
		if(array_key_exists($name, $this->values)) {
			$this->values[$name] = $value;
		}
		else {
			$trace = debug_backtrace();
			trigger_error(
			htmlentities(
				"Impossible de mettre à jour la valeur $name => $value, celle-ci n'existe pas " .
				' dans ' . $trace[0]['file'] .
				' à la ligne ' . $trace[0]['line']),
				E_USER_ERROR);
		}
	}
	
	/**
	 * 
	 * Renvoie les valeurs du formulaire
	 * @return Array name => value
	 * 
	 */
	public function getValues() {
		return $this->values;
	}
	
}

?>