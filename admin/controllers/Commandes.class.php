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
	
	public function valid() {
		
		if(isset($_GET['id_command'])) {
			
			$commande = new Commande();
			$commande = $commande->selectById($_GET['id_command']);
			$commande->setValide(1);
			$commande->update();
			$this->setInfo("La commande a été validée !");
			
		}
		
	}
	
	public function details() {
		
		if(isset($_GET['id_command'])) {
			
			$this->controllerTemplate = "Commandes_Details.template.html";
			
		}
		
	}
	
	public function getInvalidCommands() {
		
		$commands = new Commande();
		$users = new User();
		
		$commands = $commands->select("valide = 0");
		$results = array();
		
		foreach($commands as $commandObj) {
			
			$command = Model::toData($commandObj);
			$command["user"] = Model::toData($users->selectById($command['idUser']));
			$command["total"] = $commandObj->getTotal();
			$results[] = $command;
			
		}
			
		return $results;
		
	}
	
	public function getValidCommands() {
		
		$commands = new Commande();
		$users = new User();
		
		$commands = $commands->select("valide = 1");
		$results = array();
		
		foreach($commands as $commandObj) {
			
			$command = Model::toData($commandObj);
			$command["user"] = Model::toData($users->selectById($command['idUser']));
			$command["total"] = $commandObj->getTotal();
			$results[] = $command;
			
		}
			
		return $results;
		
	}
	
	public function getCommande() {
		
		if(isset($_GET['id_command'])) {
		
			$commands = new Commande();
			$users = new User();
			
			$commandObj = $commands->selectById($_GET['id_command']);
			
			$command = Model::toData($commandObj);
			$command["user"] = Model::toData($users->selectById($command['idUser']));
			$command["total"] = $commandObj->getTotal();
			
			$reservations = $commandObj->getReservations();
			
			$results = array();
			
			foreach($reservations as $reservation) {
				$item = Model::toData($reservation);
				$item["article"] = Model::toData($reservation->getArticle());
				$results[] = $item;
			}
			
			$command["reservations"] = $results;
			
			return $command;
		}
		
	}

	
}

?>