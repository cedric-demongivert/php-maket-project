<?php

class Users extends Controller {
	
	public function __construct() {
		parent::__construct("users","Users.template.html");
		$this->title="Gestion des utilisateurs";
	}
	
	public function getUsers() {
		
		$user = new User();
		
		return $user->select("removed = 0");
		
	}
	
	public function delete() {
		
		if(isset($_GET["id_user"]) && isset($_GET["force"])) {
			
			$user = new User();
			$user = $user->selectById($_GET["id_user"]);
			$user->setRemoved(1);
			$user->update();
			
			$_GET["function"] = "";
			
			$this->info = "L'utilisateur {$user->getLogin()} a bien été supprimé ";
		
		}
		
	}
	
	public function admin() {
		
		if(isset($_GET["id_user"]) && isset($_GET["admin"])) {
			
			$user = new User();
			$user = $user->selectById($_GET["id_user"]);
			$user->setAdmin(1);
			$user->update();
			
			$this->info = "L'utilisateur {$user->getLogin()} est devenu admin ";
						
		}
		else if(isset($_GET["id_user"]) && isset($_GET["noadmin"])) {
			
			$user = new User();
			$user = $user->selectById($_GET["id_user"]);
			
			if($user->getLogin() == $_SESSION['user']->getLogin()) {
				
				$this->error = "Impossible de changer le statut d'un compte utilisé";
				return;
				
			}
			
			$user->setAdmin(0);
			$user->update();
			
			$this->info = "L'utilisateur {$user->getLogin()} n'est plus admin ";
						
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