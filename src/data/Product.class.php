<?php
class Product {

	private $id;
	private $name;
	private $cost;
	private $promo;
	private $image;
	private $category;
	private $loaded;
	
	function __construct($name,$cost,$promo,$image,$category) {
		$this->name = $name;
		$this->cost = $cost;
		$this->promo = $promo;
		$this->image = $image;
		$this->category = $category;
		$this->loaded = false;
	}
	
	function __construct($bdd, $id) {
		$this->id = $id;
		$result = $bdd->prepare("SELECT * FROM Product WHERE num_produit = :id");
		$result->bindParam(":id",$id);
		$result->execute() or die("Erreur SQL Ligne 27 Product");
		if ($data = $result->fetch()) {
			$this->name = $data['nom_produit'];
			$this->cost = $data['prix'];
			$this->promo = $data['promo'];
			$this->image = $data['photo'];
			$this->category = $data['num_categorie'];
			$this->loaded = true;
		} else {
			die("Le produit qui possède l'id ".$id." n'existe pas.");
		}
	}
	
	function getName() {
		return $this->name;
	}
	
	function setName($name) {
		$this->name = $name;
	}
	
	function getCost() {
		return $this->cost;
	}
	
	function setCost($cost) {
		$this->cost = $cost;
	}
	
	function getPromo() {
		return $this->promo;
	}
	
	function setPromo($promo) {
		$this->promo = $promo;
	}
	
	function getImage() {
		return $this->image;
	}
	
	function setImage($image) {
		$this->image = $image;
	}
	
	function getCategory() {
		return $this->category;
	}
	
	function setCategory($category) {
		$this->category = $category;
	}
	
	function save($bdd) {
		if (!($this->loaded)) {
			$insert = $bdd->prepare("INSERT INTO produit (nom_produit, prix, promo, photo, num_categorie) VALUES (:name, :cost, :promo, :photo, :category)");
			$insert->bindParam(":name",$name);
			$insert->bindParam(":cost",$cost);
			$insert->bindParam(":promo",$promo);
			$insert->bindParam(":photo",$image);
			$insert->bindParam(":category",$category);
			$insert->execute() or die("Erreur lors de l'insertion d'un nouveau produit.");
		} else {
			$insert = $bdd->prepare("UPDATE produit SET nom_produit = :name, prix = :cost, promo = :promo, photo = :photo, num_categorie = :category WHERE num_produit = :id");
			$insert->bindParam(":id",$this->id);
			$insert->bindParam(":name",$name);
			$insert->bindParam(":cost",$cost);
			$insert->bindParam(":promo",$promo);
			$insert->bindParam(":photo",$image);
			$insert->bindParam(":category",$category);
			$insert->execute() or die("Erreur lors de l'insertion d'un nouveau produit.");
		}
	}
}
?>