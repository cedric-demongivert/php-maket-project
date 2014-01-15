<?php

class PanierItem {
	
	private $idArticle;
	private $quantity;
	
	public function __construct($idArticle, $quantity) {
		$this->idArticle = $idArticle;
		$this->quantity = $quantity;
	}
	
	public function getIdArticle() {
		return $this->idArticle;
	}
	
	public function getQuantity() {
		return $this->quantity;
	}
	
	public function addQuantity($quantity) {
		$this->quantity += $quantity;
	}
	
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}
	
	public function isArticle($idArticle) {
		return $this->idArticle == $idArticle;
	}
	
	public function setIdArticle($id) {
		$this->idArticle = $id;
	}
	
}

?>