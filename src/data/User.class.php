<?php

class User extends Model {
	
	public function __construct() {
		parent::__construct("users");
	}
	
	public function exist($login) {
		
		$result = $this->select("login = ".Model::$bdd->quote($login)."");
		
		return sizeof($result) != 0;
		
	}
	
	public function identify($login, $pass) {
		
		$result = $this->select("login = ".Model::$bdd->quote($login)." AND removed = 0");

		if(sizeof($result) != 0) {
			
			if(crypt($pass, $result[0]->getPass()) == $result[0]->getPass()) {
				return $result[0];
			}
			else {
				return false;
			}
			
		}
		else {
			return false;
		}
		
	}
	
	public function getCommands() {
		
		$commands = new Commande();
		
		return $commands->select("idUser = {$this->getId()}");
		
	}
	
}

?>