<?php
require_once "./src/Controller.class.php";

class Categories extends Controller {
	
	public function getData() {
		
		return array(
			"title" => "Catégories de produits",
			"meta" => "",
			"import" => ""
		);
		
	}
	
	
}

?>