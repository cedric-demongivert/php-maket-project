<?php

class Commande extends Model {
	
	public function __construct() {
		parent::__construct("commandes");
	}
	
	public function getReservations() {
		
		$reservation = new Reservation();
		return $reservation->select("idCommande = {$this->getId()}");
		
	}
	
	public function getTotal() {
		
		$reservation = new Reservation();
		$results = $reservation->query("SELECT SUM(prix) AS total FROM :table WHERE idCommande = {$this->getId()}");
		return 0+$results[0]['total'];
		
	}
	
}

?>