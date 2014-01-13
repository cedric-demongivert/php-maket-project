<?php

class Users extends Controller {
	
	public function __construct() {
		parent::__construct("users","Users.template.html");
		$this->title="Gestion des utilisateurs";
	}
	
	public function getUsers() {
		
		$user = new User();
		
		return $user->selectAll();
		
	}
	
	public function delete() {
		
		if(isset($_GET["id_user"]) && isset($_GET["force"])) {
			
			$user = new User();
			$user = $user->selectById($_GET["id_user"]);
			
			$user->remove();
			
			$_GET["function"] = "";
			
			$this->info = "L'utilisateur {$user->getLogin()} a bien été supprimé ";
		
		}
		
	}
	
	
	public function getRemove() {

		if(isset($_GET['function']) && $_GET['function']=="delete" 
				&& isset($_GET["id_user"])) {
			
			$user = new User();
			
			return $user->selectById($_GET["id_user"]);
			
		}
		
		return null;
		
	}
	
}

?>