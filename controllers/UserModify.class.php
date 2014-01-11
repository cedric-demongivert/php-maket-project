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
				
			$user = User::get($_GET["modify"], $this->bdd);
				
			if($user === false) {
				$this->error = "L'utilisateur {$user->getId()} n'existe pas en base";
			}
			else {
				$this->form['modify'] = true;
				$this->form['login'] = $user->getLogin();
				if($user->isAdmin())
				$this->form['admin'] = $user->isAdmin();
			}
				
		}
		/* créer un utilisateur */
		else {
			$this->title = "Créer un utilisateur";
			$this->form['actionType'] = "Créer";
		}

	}

	private function verifyPost() {

		if(!isset($_POST["modify"])) {
			$this->verifyPostCreate();
		}
		else {
			$this->verifyPostModify();
		}
			
	}

	public function verifyPostCreate() {
		/* Si l'on a pas renseigné un des deux champs */
		if(empty($_POST["login"]) || empty($_POST["pass"])) {
				
			$this->error = "Veuillez préciser un login ET un mot de passe";

		} // if(empty($_POST["login"]) || empty($_POST["pass"]))
		else {
			$user = User::create($_POST["login"], $_POST["pass"], isset($_POST["admin"]), $this->bdd);
				
			/* si $user === false, alors le login n'est pas unique */
			if($user === false) {
				$this->error = "Le login utilisé existe déjà.";
			}
			/* sinon on sauvegarde en base */
			else {
				$user->sav($this->bdd);
				$this->info = "L'utilisateur {$user->getLogin()} a bien été crée en base.";
				return;
			}
				
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

	public function verifyPostModify() {

		$user = User::get($_GET["modify"], $this->bdd);
		
		// Vérifications login :
		if(empty($_POST["login"])) {
			$this->error = "Le login ne peut être vide";
		}
		/* Changement de login */
		else if($_POST["login"] != $user->getLogin()) {
			
			if(User::existLogin($_POST["login"], $this->bdd) > 0) {
				$this->error = "Le login {$_POST['login']} existe déjà";
				return;
			}
			else {
				$user->setLogin($_POST['login']);
			}
			
		}
		
		// Vérifications mot de passe 
		if(!empty($_POST["last_pass"]) && empty($_POST["new_pass"])) {
					
			$this->error = "Veuillez saisir un nouveau mot de passe";
			return;
					
		}
		else if(!empty($_POST["last_pass"]) && crypt($_POST["last_pass"], $user->getPass()) == $user->getPass()){

			$user->setPass($_POST["new_pass"]);
			
		}
		else if (!empty($_POST["last_pass"])) {
					
			$this->error = "Le mot de passe saisi est invalide";
			return;
					
		}
		
		$user->setAdmin(isset($_POST["admin"]));
		$user->sav($this->bdd);
						
		$this->info = "L'utilisateur {$user->getLogin()} a bien été modifié en base.";
		
	}

	/* ------------------------------------ */
	/*		GETTER(S) :						*/
	/* ------------------------------------ */
	public function getForm() {
		return $this->form;
	}

}

?>