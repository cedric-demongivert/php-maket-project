<?php
require_once './src/data/User.class.php';

class UserList extends Controller {
	
	public function init() {

		$this->title="Liste des utilisateurs";
		$this->controllerName = "usersController";
	
	}
	
	public function getUsers() {
		return User::getAll($this->bdd);
	}
	
}

?>