<?php

class UserModify extends Controller {
	
	public $actionType;
	
	public function init() {
		
		/* modifier un utilisateur */
		if(isset($_GET) && isset($_GET['modify'])) {
			$this->title = "Modifier un utilisateur";
			$this->actionType = "Modifier";
		}
		/* créer un utilisateur */
		else {
			$this->title = "Créer un utilisateur";
			$this->actionType = "Créer";
		}
		
	}
	
	public function getActionType() {
		return $this->actionType;
	}
	
}

?>