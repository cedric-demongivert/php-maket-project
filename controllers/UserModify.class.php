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
	
	private function verifyPost() {
		
		if(empty($_POST["login"]) || empty($_POST["pass"])) {
			
			$this->error = "Veuillez préciser un login ET un mot de passe";

		}
		
		if(empty($_POST["login"])) {
			$this->form['loginClass'] = "errorField";
		}
		else {
			$this->form['login'] = $_POST['login'];
		}
			
		if(empty($_POST["pass"])) {
			$this->form['passClass'] = "errorField";
		}
		else {
			$this->form['pass'] = $_POST['pass'];
		}
			
	}
	
	public function getForm() {
		return $this->form;
	}
	
}

?>