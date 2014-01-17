<?php

class Reservation extends Model {
	
	public function __construct() {
		parent::__construct("reservations");
	}
	
	public function getCommande() {
		
		$commande = new Commande();
		return $commande->selectById($this->getIdCommande());
		
	}
	
	public function getArticle() {
		
		$article = new Article();
		return $article->selectById($this->getIdArticle());
		
	}
	
}

?>