<?php
class Product {

	private $id;
	private $name;
	private $cost;
	private $promo;
	private $image;
	private $category;
	private $loaded;
	
	public static function create($name,$cost,$promo,$image,$category) {
		
		$product = new Product();
		$product->name = $name;
		$product->cost = $cost;
		$product->promo = $promo;
		$product->image = $image;
		$product->category = $category;
		$product->loaded = false;
		
		return $product;
		
	}

	public static function get($bdd, $id) {
		$product = new Product();
		
		$product->id = $id;
		$result = $bdd->prepare("SELECT * FROM Product WHERE num_produit = :id");
		$result->bindParam(":id",$id);
		
		if(!$result->execute()) {
			print_r($result->errorInfo()); 
			die("Erreur SQL Ligne 34 Product");
		}
		
		if ($data = $result->fetch()) {
			$product->name = $data['nom_produit'];
			$product->cost = $data['prix'];
			$product->promo = $data['promo'];
			$product->image = $data['photo'];
			$product->category = $data['num_categorie'];
			$product->loaded = true;
		} else {
			die("Le produit qui possède l'id $id n'existe pas.");
		}
		
		return $product;
	}
	
	private function __construct() {
		
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getCost() {
		return $this->cost;
	}
	
	public function setCost($cost) {
		$this->cost = $cost;
	}
	
	public function getPromo() {
		return $this->promo;
	}
	
	public function setPromo($promo) {
		$this->promo = $promo;
	}
	
	public function getImage() {
		return $this->image;
	}
	
	public function setImage($image) {
		$this->image = $image;
	}
	
	public function getCategory() {
		return $this->category;
	}
	
	public function setCategory($category) {
		$this->category = $category;
	}
	
	public function save($bdd) {
		if (!($this->loaded)) {
			
			$insert = $bdd->prepare("INSERT INTO produit (nom, prix, promo, photo, id_categorie) VALUES (:name, :cost, :promo, :photo, :category)");
			$insert->bindParam(":name",$this->name);
			$insert->bindParam(":cost",$this->cost);
			$insert->bindParam(":promo",$this->promo);
			$insert->bindParam(":photo",$this->image);
			$insert->bindParam(":category",$this->category);
			
			if(!$insert->execute()) {
				print_r($insert->errorInfo()); 
				die("Erreur lors de l'insertion d'un nouveau produit.");
			}
			
			$this->id = $bdd->lastInsertId(); 
			
		} else {
			
			$insert = $bdd->prepare("UPDATE produit SET nom = :name, prix = :cost, promo = :promo, photo = :photo, id_categorie = :category WHERE id = :id");
			$insert->bindParam(":id",$this->id);
			$insert->bindParam(":name",$this->name);
			$insert->bindParam(":cost",$this->cost);
			$insert->bindParam(":promo",$this->promo);
			$insert->bindParam(":photo",$this->image);
			$insert->bindParam(":category",$this->category);
			
			if($insert->execute()) {
				print_r($insert->errorInfo()); 
				die("Erreur lors de l'insertion d'un nouveau produit.");
			}
			
		}
	}
}
?>