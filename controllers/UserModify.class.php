<?php

class UserModify extends Controller {
	
	private $form;
	
	public function init() {
		
		/* données passées en paramètres */
		if(isset($_POST)) {
			$this->verifyPost();
		}
		
		/* modifier un utilisateur */
		if(isset($_GET) && isset($_GET['modify'])) {
			$this->title = "Modifier un utilisateur";
			$this->form['actionType'] = "Modifier";
		}
		/* créer un utilisateur */
		else {
			$this->title = "Créer un utilisateur";
			$this->form['actionType'] = "Créer";
		}
		
	}
	
	public function getForm() {
		return $this->form;
	}
	
}

?>