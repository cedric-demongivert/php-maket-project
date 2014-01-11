<?php

class User {
	
	/* ------------------------------------ */
	/*		ATTRIBUT(S) :					*/
	/* ------------------------------------ */
	private $id;
	private $login;
	private $pass;
	private $admin;
	private $loaded;
	
	/* ------------------------------------ */
	/*		CONSTRUCTEUR(S) :				*/
	/* ------------------------------------ */
	/* Créer un utilisateur */
	public static function create($login, $pass, $admin, $bdd) {
		
		$user = new User();
		$user->login = $login;
		$user->pass = crypt($pass);
		$user->admin = $admin;
		$user->loaded = false;
		
		if(User::existLogin($login, $bdd) > 0) {
			return false;
		}
		else {
			return $user;
		}
		
	}
	
	/* Récupérer un utilisateur par son id (false sinon) */
	public static function get($id, $bdd) {
		
		$user = new User();
		
		$user->id = $id;
		
		$result = $bdd->prepare("SELECT * FROM user WHERE id = :id");
		$result->bindParam(":id",$id);
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		if ($data = $result->fetch()) {
			$user->login = $data["login"];
			$user->pass = $data["pass"];
			$user->admin = $data["admin"];
			$user->loaded = true;
		} else {
			return false;
		}
		
		return $user;
		
	}
	
	/* Récupérer TOUT les utilisateurs */
	public static function getAll($bdd) {
		
		$users = array();
		
		$result = $bdd->prepare("SELECT * FROM user");
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		while ($data = $result->fetch()) {
			$user = new User();
			$user->id = $data["id"];
			$user->login = $data["login"];
			$user->pass = $data["pass"];
			$user->admin = $data["admin"];
			$user->loaded = true;
			$users[] = $user;
		} 

		return $users;
		
	}
	
	/* Vérifier l'existence d'un utilisateur login + pass */
	public static function exist($login, $pass, $bdd) {
		
		$result = $bdd->prepare("SELECT * FROM user WHERE login = :login AND pass = :pass");
		$result->bindParam(":login",$login);
		$result->bindParam(":pass",crypt($pass));
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		if($result->rowCount() >= 1) {
			$data = $result->fetch();
			return $data["id"];
		}
		
		return -1;
		
	}
	
	/* Vérifier l'existence d'un utilisateur par login */
	public static function existLogin($login, $bdd) {
		
		$result = $bdd->prepare("SELECT * FROM user WHERE login = :login");
		$result->bindParam(":login",$login);
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		if($result->rowCount() >= 1) {
			$data = $result->fetch();
			return $data["id"];
		}
		
		return -1;
		
	}
	
	private function __construct() {
		
	}
	
	/* ------------------------------------ */
	/*		METHODE(S) :					*/
	/* ------------------------------------ */
	public function sav($bdd) {

		if (!($this->loaded)) {
			
			$insert = $bdd->prepare("INSERT INTO user (login, pass, admin) VALUES (:login, :pass, :admin)");
			$insert->bindParam(":login",$this->login);
			$insert->bindParam(":pass",$this->pass);
			$insert->bindParam(":admin",$this->admin);
			
			if(!$insert->execute()) {
				print_r($insert->errorInfo()); 
				die("Erreur lors de l'insertion d'un nouvel utilisateur.");
			}
			
			$this->id = $bdd->lastInsertId();
			
		} else {
			
			$insert = $bdd->prepare("UPDATE user SET login = :login, pass = :pass, admin = :admin WHERE id = :id");
			$insert->bindParam(":id",$this->id);
			$insert->bindParam(":login",$this->login);
			$insert->bindParam(":pass",$this->pass);
			$insert->bindParam(":admin",$this->admin);
			
			if($insert->execute()) {
				print_r($insert->errorInfo()); 
				die("Erreur lors de l'insertion d'un nouvel utilisateur.");
			}
			
		}
		
	}
	
	/* ------------------------------------ */
	/*		GETTER(S) :						*/
	/* ------------------------------------ */
	public function isAdmin() {
		return $this->admin;
	}
	
	public function getLogin() {
		return $this->login;
	}
	
	public function getPass() {
		return $this->pass;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setAdmin($admin) {
		$this->admin = $admin;
	}
	
	public function setLogin($login) {
		$this->login = crypt($login);
	}
	
	public function setPass($pass) {
		$this->pass = $pass;
	}
	
}

?>