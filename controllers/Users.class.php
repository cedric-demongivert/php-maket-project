<?php
require_once "./controllers/Categories.class.php";

class Users extends Controller {
	
	private $form;
	
	public function __construct() {
		parent::__construct("usersController", "Users_Connection.template.html");
		$this->title = "Inscription";
	}
	
	public function create() {
		
		if(isset($_POST) && !empty($_POST)) {
			
			$valid = true;
			$user = new User();
			
			if(empty($_POST['login'])) {
				$this->error = "Veuillez saisir un login.";
				$valid = false;
			}
			else if(empty($_POST['password'])) {
				$this->error = "Veuillez saisir un mot de passe.";
				$valid = false;
			}
			else if(empty($_POST['mail'])) {
				$this->error = "Veuillez saisir votre adresse mail.";
				$valid = false;
			}
			else if(preg_match("/\w+@\w+\.\w+/", $_POST['mail']) == false) {
				$this->error = "L'adresse saisie est invalide.";
				$valid = false;
			}
			else if($user->exist($_POST['login'])) {
				$this->error = "Le login utilisé existe déjà.";
				$valid = false;
			}
			
			if($valid) {
				
				$user->setLogin($_POST["login"]);
				$user->setPass(crypt($_POST["password"]));
				$user->setMail($_POST["mail"]);
				$user->setAdmin(0);
				
				$user->insert();
				
				$this->info = "Votre compte a bien été crée !";
			}
			else {
				$this->form['login'] = $_POST["login"];
				$this->form['password'] = $_POST["password"];
				$this->form['mail'] = $_POST["mail"];
				
				/* Formulaire */
				$this->controllerTemplate = "Users_Create.template.html";
			
			}
			
		}
		else {
			/* Formulaire */
			$this->controllerTemplate = "Users_Create.template.html";
		}
		
	}
	
	public function connect() {
		
		if(isset($_POST) && !empty($_POST)) {
			
			$valid = true;
			$user = new User();
			
			if(empty($_POST['login'])) {
				$this->error = "Veuillez saisir un login.";
				$valid = false;
			}
			else if(empty($_POST['password'])) {
				$this->error = "Veuillez saisir un mot de passe.";
				$valid = false;
			}
			
			if($valid) {
				
				$user = $user->identify($_POST['login'], $_POST['password']);
				
				if($user == false) {
					$this->error = "Le login ou le mot de passe sont incorrect";
				}
				else {
					$_SESSION['user'] = $user;
					$this->info = "Connection réussie !";
					
					$this->includeController(new Categories());
					$this->controllerTemplate = "Categories.template.html";
				}
				
				
			}
			
		}
		
	}
	
	public function disconnect() {
				session_unset();
				$this->info = "Déconnection réussie !";
					
				$this->includeController(new Categories());
				$this->controllerTemplate = "Categories.template.html";
	}

	public function getForm() {
		return $this->form;
	}
	
}

?>