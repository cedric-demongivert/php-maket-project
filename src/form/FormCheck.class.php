<?php

abstract class FormCheck {
	
	/**
	 *
	 * Vérifier la validitée d'une valeur
	 * 
	 * @param String $value valeur
	 * 
	 * @return Boolean true si ok, false sinon
	 * 
	 **/
	public abstract function check($value);
	
	/**
	 * 
	 * Appellé pour obtenir des détails concernant l'erreur
	 * 
	 * @return String message d'erreur
	 * 
	 **/
	public abstract function errorMessage();
	
}

class NoEmptyCheck extends FormCheck {
	
	public function check($value) {
		return preg_match("/\s*/", $value);
	}
	
	public function errorMessage() {
		return "Ce champ ne peut être laissé vide.";
	}
	
}

class IntegerCheck extends FormCheck {
	
	public function check($value) {
		return preg_match("/([0-9]+)?/", $value);
	}
	
	public function errorMessage() {
		return "La valeur saisie n'est pas un entier.";
	}
	
}

class FloatCheck extends FormCheck {
	
	public function check($value) {
		return preg_match("/([0-9]+(.[0-9]+)?)?/", $value);
	}
	
	public function errorMessage() {
		return "La valeur saisie n'est pas un réel.";
	}
	
}

class MailCheck extends FormCheck {
	
	public function check($value) {
		return preg_match("/(\w+@\w+.\w+)?/", $value);
	}
	
	public function errorMessage() {
		return "La valeur saisie n'est pas une adresse mail valide.";
	}
	
}

$isNotEmpty = new NoEmptyCheck();
$isInteger = new IntegerCheck();
$isFloat = new FloatCheck();
$isMail = new MailCheck();

?>