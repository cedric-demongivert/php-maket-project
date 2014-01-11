<?php
require_once './src/data/User.class.php';

class UserModify extends Controller {
	
	/* ------------------------------------ */
	/*		ATTRIBUT(S) :					*/
	/* ------------------------------------ */
	private $form;
	
	/* ------------------------------------ */
	/*		METHODE(S) :					*/
	/* ------------------------------------ */
	public function init() {
		
		$this->controllerName = "user";
		
		/* données passées en paramètres */
		if(isset($_POST) && !empty($_POST)) {
			$this->verifyPost();
		}
		
		/* modifier un utilisateur */
		if(isset($_GET) && isset($_GET['modify'])) {
			$this->title = "Modifier un utilisateur";
			$this->form['actionType'] = "Modifier";
			$this->form['args'] = "&modify={$_GET['modify']}";
			$this->form['id'] = $_GET['modify'];
		}
		/* créer un utilisateur */
		else {
			$this->title = "Créer un utilisateur";
			$this->form['actionType'] = "Créer";
		}
		
	}
	
	private function verifyPost() {
		
		/* Si l'on a pas renseigné un des deux champs */
		if(empty($_POST["login"]) || empty($_POST["pass"])) {
			
			$this->error = "Veuillez préciser un login ET un mot de passe";

		}
		/* Sinon si on tente de créer l'utilisateur */
		else if (!isset($_POST["modify"])){
			
			$user = User::create($_POST["login"], $_POST["pass"], isset($_POST["admin"]), $this->bdd);
			
			/* si $user === false, alors le login n'est pas unique */
			if($user === false) {
				$this->error = "Le login utilisé existe déjà.";
			}
			/* sinon on sauvegarde et on passe ne mode modifier */
			else {
				$user->sav($this->bdd);
				$this->info = "L'utilisateur {$user->getLogin()} a bien été crée en base.";
				$_GET['modify'] = $user->getId();
			}
			
		}
		/* Sinon modification */
		else {

			$user = User::get($_POST["modify"], $this->bdd);
			$user->setLogin($_POST["login"]);
			$user->setPass($_POST["pass"]);
			$user->setAdmin(isset($_POST["admin"]));
			$user->sav($this->bdd);
			
			$this->info = "L'utilisateur {$user->getLogin()} a bien été modifié en base.";
			
		}
		
		/* Récupération des informations pour remplir de nouveau le formulaire */
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
		
		if(isset($_POST["admin"])) {
			$this->form['admin'] = true;
		}
			
	}
	
	/* ------------------------------------ */
	/*		GETTER(S) :						*/
	/* ------------------------------------ */
	public function getForm() {
		return $this->form;
	}
	
}

?>