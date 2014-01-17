<?php
class Commandes extends Controller {
	
	private $ariane;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		parent::__construct("commandes","Commandes.template.html");
		$this->title = "Gestion des commandes";
	}
	
	public function getCommands() {
		
		$commands = new Commande();
		$users = new User();
		
		$commands = Model::toData($commands->selectAll());
		$results = array();
		
		foreach($commands as $command) {
			
			$command["user"] = Model::toData($users->selectById($command['idUser']));
			$results[] = $command;
			
		}
		
		return $results;
		
	}

	
}

?>