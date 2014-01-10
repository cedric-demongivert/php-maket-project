<?php

class ProductCategory {
	private $id;
	private $name;
	
	function __construct($name) {
		$this->name = $name;
	}
	
	function __construct($bdd,$id) {
		$result = $bdd->prepare("SELECT * FROM categorie WHERE num_categorie = :id");
		$result->bindParam(":id",$id);
		$result->execute() or die("Erreur SQL ligne 14 ProductCategory");
	}
}

?>