<?php
require_once "./controllers/PanierController.class.php";

class Users extends Controller {
	
	private $form;
	
	public function __construct() {
		parent::__construct("usersController", "Users_Connection.template.html");
		$this->title = "Inscription";
		$this->includeController(new PanierController("index.php?service=Categories"));
	}
	
	public function create() {
		
		$this->controllerTemplate = "Users_Create.template.html";
		
		if(isset($_POST) && !empty($_POST)) {
			
			$user = new User();
			
			/* Préparation des tests sur le formulaire : */
			$formFactory = new ModelFormBuilder();
			$formFactory->setModel($user);
			$this->form = $formFactory->buildForm();
			$this->form->addCheck('mail', new MailCheck());
			$this->form->addCheck('mail', new NoEmptyCheck());
			$this->form->addCheck('pass', new NoEmptyCheck());
			$this->form->addCheck('login', new NoEmptyCheck());
			
			/* Si ok */
			if(($errors = $this->form->evaluate($_POST)) === true) {
				
				/* Si login pas déjà pris */
				if($user->exist($_POST['login'])) {
					$this->setError("Le login utilisé existe déjà.");
					return;
				}
				
				/* Alors modif en base */
				$this->form->complete($_POST);
				$user = $formFactory->buildModel($this->form, new User());
				$user->setPass(crypt($_POST['pass']));
				$user->setAdmin(0);

				$user->insert();
				
				$this->setInfo("Votre compte a bien été crée, vous pouvez vous connecter.");
				
				header("Location: index.php?service=Users&function=connect");
				exit();
				
			}
			else {
				$this->error = Form::toStringErrors($errors);
				$this->form->complete($_POST);
			}
			
		}

	}
	
	public function modify() {
		
		$this->title="Modifier un utilisateur";
		$this->controllerTemplate = "Users_Modify.template.html";
		
		if(isset($_POST)) {

			$user = $_SESSION['user'];
	
			/* Préparation des tests sur le formulaire : */
			$this->form = new Form();
			$this->form->addField("mail");
			$this->form->addField("password");
			$this->form->addField("oldPassword");
			$this->form->addCheck('mail', new MailCheck());
			$check = new MultiNotNullCheck("Si vous souhaitez changer de mot de passe, veuillez indiquer 
			<br/> l'ancien ET le nouveau mot de passe.");
			$this->form->addCheck('password', $check);
			$this->form->addCheck('oldPassword', $check);
			
			/* S'il n'y a pas d'erreur */
			if(($errors = $this->form->evaluate($_POST)) === true) {
				
				if(empty($_POST["password"]) && empty($_POST["oldpassword"])) {
					return;
				}
				
				/* Cas où l'ancient mot de passe n'est pas valide */
				if(crypt($_POST['oldPassword'], $user->pass) != $user->pass) {
					$this->setError("Mot de passe incorrect");
					$this->form->complete($_POST);
					return;
				}
				
				if(!empty($_POST["password"])) {
					$user->setPass(crypt($_POST["password"]));
				}

				if(!empty($_POST["mail"])) {
					$user->setMail($_POST["mail"]);
				}
			
				$user->update();
				
				$this->setInfo("Votre compte a bien été modifié !");
			}
			else {
				
				$this->error = Form::toStringErrors($errors);
				$this->form->complete($_POST);
				
			}
			
		}
		
	}
	
	public function connect() {
		
		if(isset($_POST) && !empty($_POST)) {

			$user = new User();
			
			/* Préparation des tests sur le formulaire : */
			$formFactory = new ModelFormBuilder();
			$formFactory->setModel($user);
			$this->form = $formFactory->buildForm();
			$this->form->addCheck('login', new NoEmptyCheck());
			$this->form->addCheck('pass', new NoEmptyCheck());
			
			/* S'il n'y a pas d'erreur */
			if(($errors = $this->form->evaluate($_POST)) === true) {
				
				/* On tente d'identifier l'utilisateur */
				$user = $user->identify($_POST['login'], $_POST['pass']);
				
				/* False, c'est qu'il y a un problème */
				if($user == false) {
					$this->setError("Le login ou le mot de passe est incorrect");
				}
				else {
					
					/* Sinon connexion ok */
					$_SESSION['user'] = $user;
					$this->setInfo("Connection réussie !");
					
					header("Location: index.php");
					exit();
				}
				
				
			}
			else {
				$this->setError(Form::toStringErrors($errors));
			}
			
		}
		
	}
	
	/* Déconnection d'un utilisateur */
	public function disconnect() {
				/* On supprime la variable "user" de la session */
				session_unregister("user");
				$this->setInfo("Déconnection réussie !");
				
				/* Retour à l'accueil */
				header("Location: index.php");
				exit();
	}

	public function getForm() {
		return $this->form;
	}
	
}

class MultiNotNullCheck extends FormCheck {
	
	private $last;
	private $msg;
	
	public function __construct($msg) {
		$this->last = 0;
		$this->msg = $msg;
	}
	
	public function check($value) {
		
		$b = !empty($value) && !preg_match("/^\s+$/", $value);
		
		if(!$b && $this->last <= 0) {
			$this->last = -1;
			return true;
		}
		
		if($b && $this->last >= 0) {
			$this->last = 1;
			return true;
		}
		
		return false;
		
	}
	
	public function errorMessage() {
		return $this->msg;
	}
	
}

?>