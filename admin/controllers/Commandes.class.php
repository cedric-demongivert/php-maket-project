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
		return $commands->selectAll();
		
	}

	
}

?>